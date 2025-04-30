@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span>{{ __('supportticket::admin.all_tickets') }}</span>
                    
                    <div>
                        <div class="btn-group me-2">
                            <a href="{{ route('supportticket.admin.tickets.index') }}" class="btn btn-sm {{ !request('status') ? 'btn-primary' : 'btn-outline-secondary' }}">
                                {{ __('supportticket::admin.all') }}
                            </a>
                            <a href="{{ route('supportticket.admin.tickets.index', ['status' => 'open']) }}" class="btn btn-sm {{ request('status') === 'open' ? 'btn-success' : 'btn-outline-success' }}">
                                {{ __('supportticket::tickets.status_open') }}
                            </a>
                            <a href="{{ route('supportticket.admin.tickets.index', ['status' => 'pending']) }}" class="btn btn-sm {{ request('status') === 'pending' ? 'btn-warning' : 'btn-outline-warning' }}">
                                {{ __('supportticket::tickets.status_pending') }}
                            </a>
                            <a href="{{ route('supportticket.admin.tickets.index', ['status' => 'resolved']) }}" class="btn btn-sm {{ request('status') === 'resolved' ? 'btn-info' : 'btn-outline-info' }}">
                                {{ __('supportticket::tickets.status_resolved') }}
                            </a>
                            <a href="{{ route('supportticket.admin.tickets.index', ['status' => 'closed']) }}" class="btn btn-sm {{ request('status') === 'closed' ? 'btn-secondary' : 'btn-outline-secondary' }}">
                                {{ __('supportticket::tickets.status_closed') }}
                            </a>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success" role="alert">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if($tickets->isEmpty())
                        <div class="text-center p-5">
                            <p class="mb-4">{{ __('supportticket::admin.no_tickets') }}</p>
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>{{ __('supportticket::tickets.id') }}</th>
                                        <th>{{ __('supportticket::tickets.subject') }}</th>
                                        <th>{{ __('supportticket::admin.user') }}</th>
                                        <th>{{ __('supportticket::tickets.status') }}</th>
                                        <th>{{ __('supportticket::tickets.created_at') }}</th>
                                        <th>{{ __('supportticket::admin.last_update') }}</th>
                                        <th>{{ __('supportticket::tickets.actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($tickets as $ticket)
                                        <tr>
                                            <td>{{ $ticket->id }}</td>
                                            <td>{{ $ticket->subject }}</td>
                                            <td>
                                                @if($ticket->user)
                                                    {{ $ticket->user->name ?? $ticket->user->email ?? ($ticket->user_type . '#' . $ticket->user_id) }}
                                                @else
                                                    {{ __('supportticket::admin.anonymous') }}
                                                @endif
                                            </td>
                                            <td>
                                                <span class="badge {{ $ticket->status === 'open' ? 'bg-success' : ($ticket->status === 'pending' ? 'bg-warning' : ($ticket->status === 'resolved' ? 'bg-info' : 'bg-secondary')) }}">
                                                    {{ __('supportticket::tickets.status_'.$ticket->status) }}
                                                </span>
                                            </td>
                                            <td>{{ $ticket->created_at->format('Y-m-d H:i') }}</td>
                                            <td>{{ $ticket->updated_at->format('Y-m-d H:i') }}</td>
                                            <td>
                                                <a href="{{ route('supportticket.admin.tickets.show', $ticket) }}" class="btn btn-sm btn-outline-primary">
                                                    {{ __('supportticket::tickets.view') }}
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-4">
                            {{ $tickets->appends(request()->except('page'))->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 