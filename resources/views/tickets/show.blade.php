@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span>{{ __('supportticket::tickets.ticket_details') }}</span>
                    <a href="{{ route('supportticket.tickets.index') }}" class="btn btn-outline-secondary btn-sm">
                        {{ __('supportticket::tickets.back_to_tickets') }}
                    </a>
                </div>

                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success" role="alert">
                            {{ session('success') }}
                        </div>
                    @endif

                    <div class="d-flex justify-content-between mb-4">
                        <h5>{{ $ticket->subject }}</h5>
                        <span class="badge {{ $ticket->status === 'open' ? 'bg-success' : ($ticket->status === 'pending' ? 'bg-warning' : ($ticket->status === 'resolved' ? 'bg-info' : 'bg-secondary')) }}">
                            {{ __('supportticket::tickets.status_'.$ticket->status) }}
                        </span>
                    </div>

                    <div class="card mb-4">
                        <div class="card-body">
                            <div class="d-flex mb-2">
                                <div class="fw-bold me-auto">{{ __('supportticket::tickets.you') }}</div>
                                <div class="text-muted small">
                                    {{ $ticket->created_at->format('Y-m-d H:i') }}
                                </div>
                            </div>
                            <div>
                                {!! nl2br(e($ticket->message)) !!}
                            </div>
                        </div>
                    </div>

                    @if($replies->isNotEmpty())
                        <h6 class="mb-3">{{ __('supportticket::tickets.replies') }}</h6>

                        @foreach($replies as $reply)
                            <div class="card mb-3 {{ $reply->is_admin ? 'border-info' : '' }}">
                                <div class="card-body">
                                    <div class="d-flex mb-2">
                                        <div class="fw-bold me-auto">
                                            {{ $reply->is_admin ? __('supportticket::tickets.support_team') : __('supportticket::tickets.you') }}
                                        </div>
                                        <div class="text-muted small">
                                            {{ $reply->created_at->format('Y-m-d H:i') }}
                                        </div>
                                    </div>
                                    <div>
                                        {!! nl2br(e($reply->message)) !!}
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @endif

                    @if(!$ticket->isClosed())
                        <div class="mt-4">
                            <h6>{{ __('supportticket::tickets.reply_to_ticket') }}</h6>
                            
                            <form method="POST" action="{{ route('supportticket.tickets.reply', $ticket) }}">
                                @csrf
                                
                                <div class="mb-3">
                                    <textarea class="form-control @error('message') is-invalid @enderror" id="message" name="message" rows="3" required>{{ old('message') }}</textarea>
                                    @error('message')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                                
                                <div class="d-flex">
                                    <button type="submit" class="btn btn-primary me-2">
                                        {{ __('supportticket::tickets.submit_reply') }}
                                    </button>
                                    
                                    @if(!$ticket->isClosed())
                                        <form method="POST" action="{{ route('supportticket.tickets.close', $ticket) }}" class="ms-auto">
                                            @csrf
                                            <button type="submit" class="btn btn-outline-secondary" onclick="return confirm('{{ __('supportticket::tickets.confirm_close') }}')">
                                                {{ __('supportticket::tickets.close_ticket') }}
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </form>
                        </div>
                    @else
                        <div class="alert alert-secondary mt-4">
                            {{ __('supportticket::tickets.ticket_closed_message') }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 