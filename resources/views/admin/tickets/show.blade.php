@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span>{{ __('supportticket::admin.ticket_details') }}</span>
                    <a href="{{ route('supportticket.admin.tickets.index') }}" class="btn btn-outline-secondary btn-sm">
                        {{ __('supportticket::admin.back_to_tickets') }}
                    </a>
                </div>

                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success" role="alert">
                            {{ session('success') }}
                        </div>
                    @endif

                    <div class="d-flex justify-content-between mb-4 align-items-center">
                        <h5>{{ $ticket->subject }}</h5>
                        <div>
                            <form method="POST" action="{{ route('supportticket.admin.tickets.status', $ticket) }}" class="d-inline">
                                @csrf
                                <div class="input-group">
                                    <select name="status" class="form-select form-select-sm">
                                        <option value="open" {{ $ticket->status === 'open' ? 'selected' : '' }}>{{ __('supportticket::tickets.status_open') }}</option>
                                        <option value="pending" {{ $ticket->status === 'pending' ? 'selected' : '' }}>{{ __('supportticket::tickets.status_pending') }}</option>
                                        <option value="resolved" {{ $ticket->status === 'resolved' ? 'selected' : '' }}>{{ __('supportticket::tickets.status_resolved') }}</option>
                                        <option value="closed" {{ $ticket->status === 'closed' ? 'selected' : '' }}>{{ __('supportticket::tickets.status_closed') }}</option>
                                    </select>
                                    <button type="submit" class="btn btn-sm btn-outline-primary">{{ __('supportticket::admin.update_status') }}</button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>{{ __('supportticket::admin.user') }}:</strong> 
                                    @if($ticket->user)
                                        {{ $ticket->user->name ?? $ticket->user->email ?? ($ticket->user_type . '#' . $ticket->user_id) }}
                                    @else
                                        {{ __('supportticket::admin.anonymous') }}
                                    @endif
                                </p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>{{ __('supportticket::tickets.created_at') }}:</strong> {{ $ticket->created_at->format('Y-m-d H:i') }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="card mb-4">
                        <div class="card-body">
                            <div class="d-flex mb-2">
                                <div class="fw-bold me-auto">
                                    @if($ticket->user)
                                        {{ $ticket->user->name ?? $ticket->user->email ?? ($ticket->user_type . '#' . $ticket->user_id) }}
                                    @else
                                        {{ __('supportticket::admin.anonymous') }}
                                    @endif
                                </div>
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
                            <div class="card mb-3 {{ $reply->is_admin ? 'border-primary' : '' }}">
                                <div class="card-body">
                                    <div class="d-flex mb-2">
                                        <div class="fw-bold me-auto">
                                            @if($reply->is_admin)
                                                <span class="text-primary">{{ __('supportticket::admin.support_staff') }}</span>
                                                @if($reply->user)
                                                    ({{ $reply->user->name ?? $reply->user->email ?? 'ID: '.$reply->user_id }})
                                                @endif
                                            @else
                                                @if($reply->user)
                                                    {{ $reply->user->name ?? $reply->user->email ?? ($reply->user_type . '#' . $reply->user_id) }}
                                                @else
                                                    {{ __('supportticket::admin.anonymous') }}
                                                @endif
                                            @endif
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

                    <div class="mt-4">
                        <h6>{{ __('supportticket::admin.reply_to_ticket') }}</h6>
                        
                        <form method="POST" action="{{ route('supportticket.admin.tickets.reply', $ticket) }}">
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
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="update_status" name="update_status" value="1">
                                    <label class="form-check-label" for="update_status">{{ __('supportticket::admin.also_update_status') }}</label>
                                </div>
                            </div>

                            <div class="mb-3" id="status_selection" style="display: none;">
                                <select name="status" class="form-select">
                                    <option value="open" {{ $ticket->status === 'open' ? 'selected' : '' }}>{{ __('supportticket::tickets.status_open') }}</option>
                                    <option value="pending" {{ $ticket->status === 'pending' ? 'selected' : '' }}>{{ __('supportticket::tickets.status_pending') }}</option>
                                    <option value="resolved" {{ $ticket->status === 'resolved' ? 'selected' : '' }}>{{ __('supportticket::tickets.status_resolved') }}</option>
                                    <option value="closed" {{ $ticket->status === 'closed' ? 'selected' : '' }}>{{ __('supportticket::tickets.status_closed') }}</option>
                                </select>
                            </div>
                            
                            <button type="submit" class="btn btn-primary">
                                {{ __('supportticket::admin.submit_reply') }}
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const updateStatusCheckbox = document.getElementById('update_status');
        const statusSelection = document.getElementById('status_selection');
        
        updateStatusCheckbox.addEventListener('change', function() {
            statusSelection.style.display = this.checked ? 'block' : 'none';
        });
    });
</script>
@endpush
@endsection 