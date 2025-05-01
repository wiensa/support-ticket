<div class="card mb-4">
    <div class="card-header">
        <i class="bi bi-filter me-1"></i>{{ __('supportticket::categories.filter_by_category') }}
    </div>
    <div class="card-body">
        <form action="{{ route('supportticket.tickets.index') }}" method="GET" id="category-filter-form">
            <div class="mb-3">
                <select class="form-select" name="category" id="category-filter" onchange="this.form.submit()">
                    <option value="">{{ __('supportticket::categories.all_categories') }}</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                            {{ $category->name }} ({{ $category->tickets_count ?? 0 }})
                        </option>
                    @endforeach
                </select>
            </div>
        </form>
    </div>
</div>

<div class="card mb-4">
    <div class="card-header">
        <i class="bi bi-funnel me-1"></i>{{ __('supportticket::tickets.filter_by_status') }}
    </div>
    <div class="card-body">
        <div class="d-flex flex-wrap">
            <a href="{{ route('supportticket.tickets.index', array_merge(request()->except('status', 'page'), [])) }}" 
               class="btn {{ !request('status') ? 'btn-primary' : 'btn-outline-secondary' }} btn-sm me-2 mb-2">
                {{ __('supportticket::tickets.all_statuses') }}
            </a>
            <a href="{{ route('supportticket.tickets.index', array_merge(request()->except('status', 'page'), ['status' => 'open'])) }}" 
               class="btn {{ request('status') === 'open' ? 'btn-success' : 'btn-outline-success' }} btn-sm me-2 mb-2">
                {{ __('supportticket::tickets.status_open') }}
            </a>
            <a href="{{ route('supportticket.tickets.index', array_merge(request()->except('status', 'page'), ['status' => 'pending'])) }}" 
               class="btn {{ request('status') === 'pending' ? 'btn-warning' : 'btn-outline-warning' }} btn-sm me-2 mb-2">
                {{ __('supportticket::tickets.status_pending') }}
            </a>
            <a href="{{ route('supportticket.tickets.index', array_merge(request()->except('status', 'page'), ['status' => 'resolved'])) }}" 
               class="btn {{ request('status') === 'resolved' ? 'btn-info' : 'btn-outline-info' }} btn-sm me-2 mb-2">
                {{ __('supportticket::tickets.status_resolved') }}
            </a>
            <a href="{{ route('supportticket.tickets.index', array_merge(request()->except('status', 'page'), ['status' => 'closed'])) }}" 
               class="btn {{ request('status') === 'closed' ? 'btn-secondary' : 'btn-outline-secondary' }} btn-sm me-2 mb-2">
                {{ __('supportticket::tickets.status_closed') }}
            </a>
        </div>
    </div>
</div>

<div class="card mb-4">
    <div class="card-header">
        <i class="bi bi-search me-1"></i>{{ __('supportticket::tickets.search_tickets') }}
    </div>
    <div class="card-body">
        <form action="{{ route('supportticket.tickets.index') }}" method="GET">
            @foreach(request()->except(['search', 'page']) as $key => $value)
                <input type="hidden" name="{{ $key }}" value="{{ $value }}">
            @endforeach
            
            <div class="input-group">
                <input type="text" class="form-control" name="search" placeholder="{{ __('supportticket::tickets.search_placeholder') }}" value="{{ request('search') }}">
                <button class="btn btn-primary" type="submit">
                    <i class="bi bi-search"></i>
                </button>
                @if(request('search'))
                    <a href="{{ route('supportticket.tickets.index', request()->except(['search', 'page'])) }}" class="btn btn-outline-secondary">
                        <i class="bi bi-x-lg"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>
</div> 