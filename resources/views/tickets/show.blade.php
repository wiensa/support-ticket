@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <!-- Sol Kolon - Talep Detayları -->
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <i class="bi bi-ticket-detailed me-2"></i>{{ __('supportticket::tickets.ticket_details') }}
                    </div>
                    <div>
                        <a href="{{ route('supportticket.tickets.index') }}" class="btn btn-outline-secondary btn-sm">
                            <i class="bi bi-arrow-left me-1"></i>{{ __('supportticket::tickets.back_to_tickets') }}
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    @include('partials.alerts')

                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h5 class="mb-0">{{ $ticket->subject }}</h5>
                        <div>
                            <span class="badge {{ $ticket->status === 'open' ? 'bg-success' : ($ticket->status === 'pending' ? 'bg-warning' : ($ticket->status === 'resolved' ? 'bg-info' : 'bg-secondary')) }}">
                                {{ __('supportticket::tickets.status_'.$ticket->status) }}
                            </span>
                            
                            @if($ticket->category)
                                <span class="badge bg-light text-dark ms-1">
                                    {{ $ticket->category->name }}
                                </span>
                            @endif
                            
                            <span class="badge bg-{{ $ticket->priority === 'high' ? 'danger' : ($ticket->priority === 'medium' ? 'primary' : 'info') }} ms-1">
                                {{ __('supportticket::tickets.priority_'.$ticket->priority) }}
                            </span>
                        </div>
                    </div>

                    <!-- İlk Mesaj -->
                    <div class="card mb-4 ticket-reply user">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <div class="d-flex align-items-center">
                                    <div class="avatar bg-light text-primary rounded-circle p-2 me-2">
                                        <i class="bi bi-person-fill"></i>
                                    </div>
                                    <div>
                                        <strong>{{ __('supportticket::tickets.you') }}</strong>
                                        <div class="text-muted small">{{ $ticket->created_at->format('d.m.Y H:i') }}</div>
                                    </div>
                                </div>
                            </div>
                            <div class="ticket-message">
                                {!! nl2br(e($ticket->message)) !!}
                            </div>
                        </div>
                    </div>

                    @if($ticket->attachments->isNotEmpty())
                        <div class="attachments mb-4">
                            <h6><i class="bi bi-paperclip me-1"></i>{{ __('supportticket::attachments.ticket_attachments') }}</h6>
                            <div class="d-flex flex-wrap">
                                @foreach($ticket->attachments as $attachment)
                                    <div class="attachment-item me-3 mb-3">
                                        <div class="card" style="width: 120px;">
                                            <div class="card-body p-2 text-center">
                                                @if(in_array($attachment->extension, ['jpg', 'jpeg', 'png', 'gif']))
                                                    <img src="{{ route('supportticket.attachments.download', $attachment) }}" class="img-thumbnail mb-2" style="height: 60px; object-fit: cover;">
                                                @else
                                                    <i class="bi bi-file-earmark-{{ $attachment->extension === 'pdf' ? 'pdf' : ($attachment->extension === 'doc' || $attachment->extension === 'docx' ? 'word' : 'text') }} display-4 mb-2"></i>
                                                @endif
                                                <p class="card-text small text-truncate" title="{{ $attachment->original_name }}">
                                                    {{ $attachment->original_name }}
                                                </p>
                                                <a href="{{ route('supportticket.attachments.download', $attachment) }}" class="btn btn-sm btn-outline-primary w-100">
                                                    <i class="bi bi-download"></i>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Yanıtlar -->
                    @if($replies->isNotEmpty())
                        <h6 class="mb-3">{{ __('supportticket::tickets.replies') }}</h6>

                        @foreach($replies as $reply)
                            <div class="card mb-3 ticket-reply {{ $reply->is_admin ? 'admin' : 'user' }}">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <div class="d-flex align-items-center">
                                            <div class="avatar {{ $reply->is_admin ? 'bg-info text-white' : 'bg-light text-primary' }} rounded-circle p-2 me-2">
                                                <i class="bi {{ $reply->is_admin ? 'bi-headset' : 'bi-person-fill' }}"></i>
                                            </div>
                                            <div>
                                                <strong>{{ $reply->is_admin ? __('supportticket::tickets.support_team') : __('supportticket::tickets.you') }}</strong>
                                                <div class="text-muted small">{{ $reply->created_at->format('d.m.Y H:i') }}</div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="ticket-message">
                                        {!! nl2br(e($reply->message)) !!}
                                    </div>

                                    @if($reply->attachments->isNotEmpty())
                                        <div class="border-top mt-3 pt-3">
                                            <h6 class="small"><i class="bi bi-paperclip me-1"></i>{{ __('supportticket::attachments.attached_files') }}</h6>
                                            <div class="d-flex flex-wrap">
                                                @foreach($reply->attachments as $attachment)
                                                    <div class="me-2 mb-2">
                                                        <a href="{{ route('supportticket.attachments.download', $attachment) }}" class="btn btn-sm btn-outline-secondary">
                                                            <i class="bi bi-file-earmark"></i> {{ $attachment->original_name }}
                                                        </a>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    @endif

                    <!-- Yanıt Formu -->
                    @if(!$ticket->isClosed())
                        <div class="mt-4">
                            <h6><i class="bi bi-reply me-1"></i>{{ __('supportticket::tickets.reply_to_ticket') }}</h6>
                            
                            <form method="POST" action="{{ route('supportticket.tickets.reply', $ticket) }}" enctype="multipart/form-data">
                                @csrf
                                
                                <div class="mb-3">
                                    <textarea class="form-control @error('message') is-invalid @enderror" id="message" name="message" rows="3" required>{{ old('message') }}</textarea>
                                    @error('message')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="attachments" class="form-label">{{ __('supportticket::attachments.add_attachments') }}</label>
                                    <input type="file" class="form-control @error('attachments.*') is-invalid @enderror" id="attachments" name="attachments[]" multiple>
                                    <div class="form-text">{{ __('supportticket::attachments.allowed_files', ['types' => '.jpg, .jpeg, .png, .pdf, .doc, .docx, .zip']) }}</div>
                                    @error('attachments.*')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                                
                                <div class="d-flex">
                                    <button type="submit" class="btn btn-primary me-2">
                                        <i class="bi bi-send me-1"></i>{{ __('supportticket::tickets.submit_reply') }}
                                    </button>
                                    
                                    @if(!$ticket->isClosed())
                                        <form method="POST" action="{{ route('supportticket.tickets.close', $ticket) }}" class="ms-auto">
                                            @csrf
                                            <button type="submit" class="btn btn-outline-secondary" onclick="return confirm('{{ __('supportticket::tickets.confirm_close') }}')">
                                                <i class="bi bi-x-circle me-1"></i>{{ __('supportticket::tickets.close_ticket') }}
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </form>
                        </div>
                    @else
                        <div class="alert alert-secondary mt-4">
                            <i class="bi bi-info-circle me-2"></i>{{ __('supportticket::tickets.ticket_closed_message') }}
                            
                            <form method="POST" action="{{ route('supportticket.tickets.reopen', $ticket) }}" class="mt-3">
                                @csrf
                                <button type="submit" class="btn btn-outline-primary btn-sm">
                                    <i class="bi bi-arrow-repeat me-1"></i>{{ __('supportticket::tickets.reopen_ticket') }}
                                </button>
                            </form>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Sağ Kolon - Bilgiler ve Eylemler -->
        <div class="col-md-4">
            <!-- Talep Bilgileri -->
            <div class="card mb-4">
                <div class="card-header">
                    <i class="bi bi-info-circle me-1"></i>{{ __('supportticket::tickets.ticket_info') }}
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between px-0">
                            <span>{{ __('supportticket::tickets.id') }}:</span>
                            <span class="text-muted">#{{ $ticket->id }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between px-0">
                            <span>{{ __('supportticket::tickets.created_at') }}:</span>
                            <span class="text-muted">{{ $ticket->created_at->format('d.m.Y H:i') }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between px-0">
                            <span>{{ __('supportticket::tickets.last_update') }}:</span>
                            <span class="text-muted">{{ $ticket->updated_at->format('d.m.Y H:i') }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between px-0">
                            <span>{{ __('supportticket::tickets.status') }}:</span>
                            <span>
                                <span class="badge {{ $ticket->status === 'open' ? 'bg-success' : ($ticket->status === 'pending' ? 'bg-warning' : ($ticket->status === 'resolved' ? 'bg-info' : 'bg-secondary')) }}">
                                    {{ __('supportticket::tickets.status_'.$ticket->status) }}
                                </span>
                            </span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between px-0">
                            <span>{{ __('supportticket::tickets.priority') }}:</span>
                            <span>
                                <span class="badge bg-{{ $ticket->priority === 'high' ? 'danger' : ($ticket->priority === 'medium' ? 'primary' : 'info') }}">
                                    {{ __('supportticket::tickets.priority_'.$ticket->priority) }}
                                </span>
                            </span>
                        </li>
                        @if($ticket->category)
                            <li class="list-group-item d-flex justify-content-between px-0">
                                <span>{{ __('supportticket::tickets.category') }}:</span>
                                <span class="badge bg-light text-dark">
                                    {{ $ticket->category->name }}
                                </span>
                            </li>
                        @endif
                    </ul>

                    @if(!$ticket->isClosed())
                        <div class="mt-3 d-grid gap-2">
                            <form method="POST" action="{{ route('supportticket.tickets.close', $ticket) }}">
                                @csrf
                                <button type="submit" class="btn btn-outline-secondary btn-sm w-100" onclick="return confirm('{{ __('supportticket::tickets.confirm_close') }}')">
                                    <i class="bi bi-x-circle me-1"></i>{{ __('supportticket::tickets.close_ticket') }}
                                </button>
                            </form>
                        </div>
                    @else
                        <div class="mt-3 d-grid gap-2">
                            <form method="POST" action="{{ route('supportticket.tickets.reopen', $ticket) }}">
                                @csrf
                                <button type="submit" class="btn btn-outline-primary btn-sm w-100">
                                    <i class="bi bi-arrow-repeat me-1"></i>{{ __('supportticket::tickets.reopen_ticket') }}
                                </button>
                            </form>
                        </div>
                    @endif
                </div>
            </div>

            <!-- İlgili Talepler -->
            @if(isset($relatedTickets) && $relatedTickets->isNotEmpty())
                <div class="card mb-4">
                    <div class="card-header">
                        <i class="bi bi-link-45deg me-1"></i>{{ __('supportticket::tickets.related_tickets') }}
                    </div>
                    <div class="card-body p-0">
                        <div class="list-group list-group-flush">
                            @foreach($relatedTickets as $relatedTicket)
                                <a href="{{ route('supportticket.tickets.show', $relatedTicket) }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                                    <div class="d-flex flex-column">
                                        <span class="text-truncate" style="max-width: 200px;">{{ $relatedTicket->subject }}</span>
                                        <small class="text-muted">{{ $relatedTicket->created_at->format('d.m.Y') }}</small>
                                    </div>
                                    <span class="badge {{ $relatedTicket->status === 'open' ? 'bg-success' : ($relatedTicket->status === 'pending' ? 'bg-warning' : ($relatedTicket->status === 'resolved' ? 'bg-info' : 'bg-secondary')) }}">
                                        {{ __('supportticket::tickets.status_'.$relatedTicket->status) }}
                                    </span>
                                </a>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif
            
            <!-- Dökümanlar -->
            <div class="card mb-4">
                <div class="card-header">
                    <i class="bi bi-file-earmark-text me-1"></i>{{ __('supportticket::common.documentation') }}
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        <a href="#" class="list-group-item list-group-item-action d-flex align-items-center">
                            <i class="bi bi-question-circle me-2"></i> {{ __('supportticket::common.faq') }}
                        </a>
                        <a href="#" class="list-group-item list-group-item-action d-flex align-items-center">
                            <i class="bi bi-book me-2"></i> {{ __('supportticket::common.user_guide') }}
                        </a>
                        <a href="#" class="list-group-item list-group-item-action d-flex align-items-center">
                            <i class="bi bi-life-preserver me-2"></i> {{ __('supportticket::common.get_support') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .avatar {
        width: 40px;
        height: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .ticket-message {
        white-space: pre-line;
    }
</style>
@endpush
@endsection 