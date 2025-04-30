@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span>{{ __('supportticket::tickets.my_tickets') }}</span>
                    <a href="{{ route('supportticket.tickets.create') }}" class="btn btn-primary btn-sm">
                        {{ __('supportticket::tickets.create_ticket') }}
                    </a>
                </div>

                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success" role="alert">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if($tickets->isEmpty())
                        <div class="text-center p-5">
                            <p class="mb-4">{{ __('supportticket::tickets.no_tickets') }}</p>
                            <a href="{{ route('supportticket.tickets.create') }}" class="btn btn-outline-primary">
                                {{ __('supportticket::tickets.create_first_ticket') }}
                            </a>
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>{{ __('supportticket::tickets.id') }}</th>
                                        <th>{{ __('supportticket::tickets.subject') }}</th>
                                        <th>{{ __('supportticket::tickets.status') }}</th>
                                        <th>{{ __('supportticket::tickets.created_at') }}</th>
                                        <th>{{ __('supportticket::tickets.actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($tickets as $ticket)
                                        <tr>
                                            <td>{{ $ticket->id }}</td>
                                            <td>{{ $ticket->subject }}</td>
                                            <td>
                                                <span class="badge {{ $ticket->status === 'open' ? 'bg-success' : ($ticket->status === 'pending' ? 'bg-warning' : ($ticket->status === 'resolved' ? 'bg-info' : 'bg-secondary')) }}">
                                                    {{ __('supportticket::tickets.status_'.$ticket->status) }}
                                                </span>
                                            </td>
                                            <td>{{ $ticket->created_at->format('Y-m-d H:i') }}</td>
                                            <td>
                                                <a href="{{ route('supportticket.tickets.show', $ticket) }}" class="btn btn-sm btn-outline-primary">
                                                    {{ __('supportticket::tickets.view') }}
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-4">
                            {{ $tickets->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 