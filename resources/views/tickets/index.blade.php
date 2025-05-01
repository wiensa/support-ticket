@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <!-- Filtreler ve Arama -->
        <div class="col-md-3">
            @include('partials.category-filter')
        </div>

        <!-- Ana içerik -->
        <div class="col-md-9">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span>
                        <i class="bi bi-ticket-perforated me-2"></i>{{ __('supportticket::tickets.my_tickets') }}
                        @if(request('search'))
                            <small class="text-muted ms-2">
                                {{ __('supportticket::tickets.search_results_for', ['query' => request('search')]) }}
                            </small>
                        @endif
                    </span>
                    <a href="{{ route('supportticket.tickets.create') }}" class="btn btn-primary btn-sm">
                        <i class="bi bi-plus-lg me-1"></i>{{ __('supportticket::tickets.create_ticket') }}
                    </a>
                </div>

                <div class="card-body">
                    @include('partials.alerts')

                    @if($tickets->isEmpty())
                        <div class="text-center p-5">
                            <div class="mb-4">
                                <i class="bi bi-ticket-perforated display-1 text-muted"></i>
                            </div>
                            <p class="mb-4">{{ __('supportticket::tickets.no_tickets') }}</p>
                            <a href="{{ route('supportticket.tickets.create') }}" class="btn btn-outline-primary">
                                <i class="bi bi-plus-lg me-1"></i>{{ __('supportticket::tickets.create_first_ticket') }}
                            </a>
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>#</th>
                                        <th>{{ __('supportticket::tickets.subject') }}</th>
                                        <th>{{ __('supportticket::tickets.status') }}</th>
                                        <th>{{ __('supportticket::tickets.created_at') }}</th>
                                        <th class="text-end">{{ __('supportticket::tickets.actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($tickets as $ticket)
                                        <tr>
                                            <td>{{ $ticket->id }}</td>
                                            <td>
                                                <a href="{{ route('supportticket.tickets.show', $ticket) }}" class="text-decoration-none">
                                                    {{ $ticket->subject }}
                                                </a>
                                                @if($ticket->category)
                                                    <div>
                                                        <span class="badge bg-light text-dark">
                                                            {{ $ticket->category->name }}
                                                        </span>
                                                    </div>
                                                @endif
                                                @if($ticket->last_reply_at && $ticket->last_reply_at > auth()->user()->last_login_at)
                                                    <span class="badge bg-danger">{{ __('supportticket::tickets.new_reply') }}</span>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="badge {{ $ticket->status === 'open' ? 'bg-success' : ($ticket->status === 'pending' ? 'bg-warning' : ($ticket->status === 'resolved' ? 'bg-info' : 'bg-secondary')) }}">
                                                    {{ __('supportticket::tickets.status_'.$ticket->status) }}
                                                </span>
                                            </td>
                                            <td>
                                                <div>{{ $ticket->created_at->format('Y-m-d') }}</div>
                                                <small class="text-muted">{{ $ticket->created_at->format('H:i') }}</small>
                                            </td>
                                            <td class="text-end">
                                                <a href="{{ route('supportticket.tickets.show', $ticket) }}" class="btn btn-sm btn-outline-primary">
                                                    <i class="bi bi-eye me-1"></i>{{ __('supportticket::tickets.view') }}
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-4 d-flex justify-content-center">
                            {{ $tickets->appends(request()->except('page'))->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 