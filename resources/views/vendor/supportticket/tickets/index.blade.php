@extends('supportticket::layouts.app')

@section('title', 'Destek Taleplerim')
@section('header', 'Destek Taleplerim')

@section('header-actions')
    <a href="{{ route('supportticket.tickets.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle"></i> Yeni Talep Oluştur
    </a>
@endsection

@section('content')
    <div class="card">
        <div class="card-header bg-white">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h5 class="mb-0">Tüm Destek Taleplerim</h5>
                </div>
                <div class="col-md-6">
                    <form action="{{ route('supportticket.tickets.index') }}" method="get" class="mt-3 mt-md-0">
                        <div class="input-group">
                            <select name="status" class="form-select" onchange="this.form.submit()">
                                <option value="">Tüm Durumlar</option>
                                <option value="open" @if(request('status') == 'open') selected @endif>Açık</option>
                                <option value="pending" @if(request('status') == 'pending') selected @endif>Beklemede</option>
                                <option value="resolved" @if(request('status') == 'resolved') selected @endif>Çözüldü</option>
                                <option value="closed" @if(request('status') == 'closed') selected @endif>Kapalı</option>
                            </select>
                            @if($categories->isNotEmpty())
                                <select name="category" class="form-select" onchange="this.form.submit()">
                                    <option value="">Tüm Kategoriler</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}" @if(request('category') == $category->id) selected @endif>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                            @endif
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="card-body p-0">
            @if($tickets->isEmpty())
                <div class="text-center p-5">
                    <i class="bi bi-inbox display-1 text-muted"></i>
                    <p class="mt-3">Henüz hiç destek talebiniz bulunmuyor.</p>
                    <a href="{{ route('supportticket.tickets.create') }}" class="btn btn-primary">
                        <i class="bi bi-plus-circle"></i> Yeni Talep Oluştur
                    </a>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>ID</th>
                                <th>Konu</th>
                                <th>Kategori</th>
                                <th>Durum</th>
                                <th>Öncelik</th>
                                <th>Son Güncelleme</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($tickets as $ticket)
                                <tr>
                                    <td class="align-middle">#{{ substr($ticket->id, 0, 8) }}</td>
                                    <td class="align-middle">
                                        <a href="{{ route('supportticket.tickets.show', $ticket) }}" class="text-decoration-none">
                                            {{ $ticket->subject }}
                                        </a>
                                    </td>
                                    <td class="align-middle">
                                        @if($ticket->categoryRelation)
                                            <span class="badge" style="background-color: {{ $ticket->categoryRelation->color ?? '#6c757d' }}">
                                                {{ $ticket->categoryRelation->name }}
                                            </span>
                                        @else
                                            <span class="badge bg-secondary">Genel</span>
                                        @endif
                                    </td>
                                    <td class="align-middle">
                                        @switch($ticket->status)
                                            @case('open')
                                                <span class="badge bg-success">Açık</span>
                                                @break
                                            @case('pending')
                                                <span class="badge bg-warning text-dark">Beklemede</span>
                                                @break
                                            @case('resolved')
                                                <span class="badge bg-info">Çözüldü</span>
                                                @break
                                            @case('closed')
                                                <span class="badge bg-secondary">Kapalı</span>
                                                @break
                                            @default
                                                <span class="badge bg-secondary">{{ $ticket->status }}</span>
                                        @endswitch
                                    </td>
                                    <td class="align-middle">
                                        @switch($ticket->priority)
                                            @case('low')
                                                <span class="badge bg-info">Düşük</span>
                                                @break
                                            @case('medium')
                                                <span class="badge bg-primary">Orta</span>
                                                @break
                                            @case('high')
                                                <span class="badge bg-warning text-dark">Yüksek</span>
                                                @break
                                            @case('urgent')
                                                <span class="badge bg-danger">Acil</span>
                                                @break
                                            @default
                                                <span class="badge bg-secondary">{{ $ticket->priority }}</span>
                                        @endswitch
                                    </td>
                                    <td class="align-middle">{{ $ticket->updated_at->diffForHumans() }}</td>
                                    <td class="align-middle text-end">
                                        <a href="{{ route('supportticket.tickets.show', $ticket) }}" class="btn btn-sm btn-outline-primary">
                                            <i class="bi bi-eye"></i> Görüntüle
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
        @if($tickets->hasPages())
            <div class="card-footer">
                {{ $tickets->withQueryString()->links() }}
            </div>
        @endif
    </div>
@endsection 