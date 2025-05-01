@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <!-- Sol Kolon - Talep Detayları -->
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <i class="bi bi-ticket-detailed me-2"></i>{{ __('supportticket::admin.ticket_details') }}
                    </div>
                    <div>
                        <a href="{{ route('supportticket.admin.tickets.index') }}" class="btn btn-outline-secondary btn-sm">
                            <i class="bi bi-arrow-left me-1"></i>{{ __('supportticket::admin.back_to_tickets') }}
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    @include('partials.alerts')

                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h5 class="mb-0">{{ $ticket->subject }}</h5>
                        <div>
                            <div class="btn-group">
                                <button type="button" class="btn btn-sm {{ $ticket->status === 'open' ? 'btn-success' : 'btn-outline-success' }} dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                                    {{ __('supportticket::tickets.status_'.$ticket->status) }}
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li>
                                        <form method="POST" action="{{ route('supportticket.admin.tickets.status', [$ticket, 'open']) }}">
                                            @csrf
                                            <button type="submit" class="dropdown-item {{ $ticket->status === 'open' ? 'active' : '' }}">
                                                {{ __('supportticket::tickets.status_open') }}
                                            </button>
                                        </form>
                                    </li>
                                    <li>
                                        <form method="POST" action="{{ route('supportticket.admin.tickets.status', [$ticket, 'pending']) }}">
                                            @csrf
                                            <button type="submit" class="dropdown-item {{ $ticket->status === 'pending' ? 'active' : '' }}">
                                                {{ __('supportticket::tickets.status_pending') }}
                                            </button>
                                        </form>
                                    </li>
                                    <li>
                                        <form method="POST" action="{{ route('supportticket.admin.tickets.status', [$ticket, 'resolved']) }}">
                                            @csrf
                                            <button type="submit" class="dropdown-item {{ $ticket->status === 'resolved' ? 'active' : '' }}">
                                                {{ __('supportticket::tickets.status_resolved') }}
                                            </button>
                                        </form>
                                    </li>
                                    <li>
                                        <form method="POST" action="{{ route('supportticket.admin.tickets.status', [$ticket, 'closed']) }}">
                                            @csrf
                                            <button type="submit" class="dropdown-item {{ $ticket->status === 'closed' ? 'active' : '' }}">
                                                {{ __('supportticket::tickets.status_closed') }}
                                            </button>
                                        </form>
                                    </li>
                                </ul>
                            </div>
                            
                            <span class="badge bg-{{ $ticket->priority === 'high' ? 'danger' : ($ticket->priority === 'medium' ? 'primary' : 'info') }} ms-1">
                                {{ __('supportticket::tickets.priority_'.$ticket->priority) }}
                            </span>
                            
                            @if($ticket->category)
                                <span class="badge bg-light text-dark ms-1">
                                    {{ $ticket->category->name }}
                                </span>
                            @endif
                        </div>
                    </div>

                    <div class="alert alert-info mb-4">
                        <div class="d-flex">
                            <div class="me-3">
                                <i class="bi bi-person-circle display-6"></i>
                            </div>
                            <div>
                                <h6>{{ __('supportticket::admin.customer_info') }}</h6>
                                <p class="mb-1">
                                    <strong>{{ __('supportticket::admin.name') }}:</strong> 
                                    {{ $ticket->user->name ?? __('supportticket::admin.unknown') }}
                                </p>
                                <p class="mb-1">
                                    <strong>{{ __('supportticket::admin.email') }}:</strong> 
                                    {{ $ticket->user->email ?? __('supportticket::admin.unknown') }}
                                </p>
                                <p class="mb-0">
                                    <strong>{{ __('supportticket::admin.id') }}:</strong> 
                                    {{ $ticket->user_id ?? __('supportticket::admin.unknown') }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- İlk Mesaj -->
                    <div class="card mb-4 ticket-reply">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <div class="d-flex align-items-center">
                                    <div class="avatar bg-light text-primary rounded-circle p-2 me-2">
                                        <i class="bi bi-person-fill"></i>
                                    </div>
                                    <div>
                                        <strong>{{ $ticket->user->name ?? __('supportticket::admin.customer') }}</strong>
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
                            <div class="card mb-3 ticket-reply {{ $reply->is_admin ? 'admin' : '' }}">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <div class="d-flex align-items-center">
                                            <div class="avatar {{ $reply->is_admin ? 'bg-info text-white' : 'bg-light text-primary' }} rounded-circle p-2 me-2">
                                                <i class="bi {{ $reply->is_admin ? 'bi-headset' : 'bi-person-fill' }}"></i>
                                            </div>
                                            <div>
                                                <strong>{{ $reply->is_admin ? auth()->user()->name : ($ticket->user->name ?? __('supportticket::admin.customer')) }}</strong>
                                                <div class="text-muted small">{{ $reply->created_at->format('d.m.Y H:i') }}</div>
                                            </div>
                                        </div>
                                        
                                        @if($reply->is_admin)
                                            <div class="dropdown">
                                                <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                    <i class="bi bi-three-dots"></i>
                                                </button>
                                                <ul class="dropdown-menu dropdown-menu-end">
                                                    <li>
                                                        <a href="{{ route('supportticket.admin.tickets.replies.edit', [$ticket, $reply]) }}" class="dropdown-item">
                                                            <i class="bi bi-pencil me-1"></i>{{ __('supportticket::admin.edit') }}
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <form method="POST" action="{{ route('supportticket.admin.tickets.replies.destroy', [$ticket, $reply]) }}" onsubmit="return confirm('{{ __('supportticket::admin.confirm_delete_reply') }}')">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="dropdown-item text-danger">
                                                                <i class="bi bi-trash me-1"></i>{{ __('supportticket::admin.delete') }}
                                                            </button>
                                                        </form>
                                                    </li>
                                                </ul>
                                            </div>
                                        @endif
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
                            <h6><i class="bi bi-reply me-1"></i>{{ __('supportticket::admin.reply_to_ticket') }}</h6>
                            
                            <form method="POST" action="{{ route('supportticket.admin.tickets.reply', $ticket) }}" enctype="multipart/form-data">
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
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" value="1" id="mark_resolved" name="mark_resolved">
                                        <label class="form-check-label" for="mark_resolved">
                                            {{ __('supportticket::admin.mark_as_resolved') }}
                                        </label>
                                    </div>
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
                                        <i class="bi bi-send me-1"></i>{{ __('supportticket::admin.submit_reply') }}
                                    </button>
                                    
                                    @if(!$ticket->isClosed())
                                        <div class="btn-group ms-auto">
                                            <button type="button" class="btn btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                                                {{ __('supportticket::admin.more_actions') }} <span class="caret"></span>
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end">
                                                <li>
                                                    <form method="POST" action="{{ route('supportticket.admin.tickets.status', [$ticket, 'resolved']) }}">
                                                        @csrf
                                                        <button type="submit" class="dropdown-item">
                                                            <i class="bi bi-check-circle me-1"></i>{{ __('supportticket::admin.mark_resolved') }}
                                                        </button>
                                                    </form>
                                                </li>
                                                <li>
                                                    <form method="POST" action="{{ route('supportticket.admin.tickets.status', [$ticket, 'closed']) }}">
                                                        @csrf
                                                        <button type="submit" class="dropdown-item">
                                                            <i class="bi bi-x-circle me-1"></i>{{ __('supportticket::admin.close_ticket') }}
                                                        </button>
                                                    </form>
                                                </li>
                                                <li><hr class="dropdown-divider"></li>
                                                <li>
                                                    <a href="{{ route('supportticket.admin.tickets.edit', $ticket) }}" class="dropdown-item">
                                                        <i class="bi bi-pencil me-1"></i>{{ __('supportticket::admin.edit_ticket') }}
                                                    </a>
                                                </li>
                                                <li>
                                                    <form method="POST" action="{{ route('supportticket.admin.tickets.destroy', $ticket) }}" onsubmit="return confirm('{{ __('supportticket::admin.confirm_delete_ticket') }}')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="dropdown-item text-danger">
                                                            <i class="bi bi-trash me-1"></i>{{ __('supportticket::admin.delete_ticket') }}
                                                        </button>
                                                    </form>
                                                </li>
                                            </ul>
                                        </div>
                                    @endif
                                </div>
                            </form>
                        </div>
                    @else
                        <div class="alert alert-secondary mt-4">
                            <i class="bi bi-info-circle me-2"></i>{{ __('supportticket::tickets.ticket_closed_message') }}
                            
                            <form method="POST" action="{{ route('supportticket.admin.tickets.status', [$ticket, 'open']) }}" class="mt-3">
                                @csrf
                                <button type="submit" class="btn btn-outline-primary btn-sm">
                                    <i class="bi bi-arrow-repeat me-1"></i>{{ __('supportticket::admin.reopen_ticket') }}
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
                    <i class="bi bi-info-circle me-1"></i>{{ __('supportticket::admin.ticket_info') }}
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
                </div>
            </div>

            <!-- Talep Düzenleme -->
            <div class="card mb-4">
                <div class="card-header">
                    <i class="bi bi-gear me-1"></i>{{ __('supportticket::admin.manage_ticket') }}
                </div>
                <div class="card-body">
                    <!-- Durum Değiştirme -->
                    <form method="POST" action="{{ route('supportticket.admin.tickets.update', $ticket) }}" class="mb-3">
                        @csrf
                        @method('PUT')
                        
                        <div class="mb-3">
                            <label for="priority" class="form-label">{{ __('supportticket::tickets.priority') }}</label>
                            <select class="form-select form-select-sm" id="priority" name="priority">
                                <option value="low" {{ $ticket->priority === 'low' ? 'selected' : '' }}>{{ __('supportticket::tickets.priority_low') }}</option>
                                <option value="medium" {{ $ticket->priority === 'medium' ? 'selected' : '' }}>{{ __('supportticket::tickets.priority_medium') }}</option>
                                <option value="high" {{ $ticket->priority === 'high' ? 'selected' : '' }}>{{ __('supportticket::tickets.priority_high') }}</option>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label for="category_id" class="form-label">{{ __('supportticket::tickets.category') }}</label>
                            <select class="form-select form-select-sm" id="category_id" name="category_id">
                                <option value="">{{ __('supportticket::tickets.no_category') }}</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ $ticket->category_id == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label for="assign_to" class="form-label">{{ __('supportticket::admin.assign_to') }}</label>
                            <select class="form-select form-select-sm" id="assign_to" name="assign_to">
                                <option value="">{{ __('supportticket::admin.unassigned') }}</option>
                                @foreach($admins as $admin)
                                    <option value="{{ $admin->id }}" {{ $ticket->assigned_to == $admin->id ? 'selected' : '' }}>
                                        {{ $admin->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary btn-sm">
                                <i class="bi bi-save me-1"></i>{{ __('supportticket::admin.update_ticket') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Dahili Notlar -->
            <div class="card mb-4">
                <div class="card-header">
                    <i class="bi bi-sticky me-1"></i>{{ __('supportticket::admin.internal_notes') }}
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('supportticket.admin.tickets.notes.store', $ticket) }}">
                        @csrf
                        <div class="mb-3">
                            <textarea class="form-control form-control-sm" id="note" name="note" rows="3" placeholder="{{ __('supportticket::admin.add_note_placeholder') }}"></textarea>
                        </div>
                        <div class="d-grid">
                            <button type="submit" class="btn btn-outline-primary btn-sm">
                                <i class="bi bi-plus-lg me-1"></i>{{ __('supportticket::admin.add_note') }}
                            </button>
                        </div>
                    </form>
                    
                    @if($notes && $notes->isNotEmpty())
                        <hr>
                        <div class="notes-list">
                            @foreach($notes as $note)
                                <div class="note mb-3">
                                    <div class="note-header d-flex justify-content-between">
                                        <strong>{{ $note->admin->name ?? __('supportticket::admin.admin') }}</strong>
                                        <small class="text-muted">{{ $note->created_at->format('d.m.Y H:i') }}</small>
                                    </div>
                                    <div class="note-body">
                                        {{ $note->content }}
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
            
            <!-- İşlem Geçmişi -->
            <div class="card mb-4">
                <div class="card-header">
                    <i class="bi bi-clock-history me-1"></i>{{ __('supportticket::admin.ticket_history') }}
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        @foreach($history as $entry)
                            <div class="list-group-item py-2 px-3">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <i class="bi {{ $entry->icon ?? 'bi-circle' }} me-1"></i>
                                        {{ $entry->description }}
                                    </div>
                                    <small class="text-muted">{{ $entry->created_at->format('d.m.Y H:i') }}</small>
                                </div>
                            </div>
                        @endforeach
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
    .ticket-reply.admin {
        border-left: 3px solid #17a2b8;
    }
    .note {
        padding: 8px;
        background-color: #f8f9fa;
        border-radius: 4px;
    }
</style>
@endpush
@endsection 