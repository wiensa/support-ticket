@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span>
                        <i class="bi bi-plus-circle me-2"></i>{{ __('supportticket::tickets.create_new_ticket') }}
                    </span>
                    <a href="{{ route('supportticket.tickets.index') }}" class="btn btn-outline-secondary btn-sm">
                        <i class="bi bi-arrow-left me-1"></i>{{ __('supportticket::tickets.back_to_tickets') }}
                    </a>
                </div>

                <div class="card-body">
                    @include('partials.alerts')

                    <form method="POST" action="{{ route('supportticket.tickets.store') }}" enctype="multipart/form-data">
                        @csrf

                        <div class="mb-3">
                            <label for="subject" class="form-label">{{ __('supportticket::tickets.subject') }} <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('subject') is-invalid @enderror" id="subject" name="subject" value="{{ old('subject') }}" required autofocus>
                            @error('subject')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="category_id" class="form-label">{{ __('supportticket::tickets.category') }}</label>
                            <select class="form-select @error('category_id') is-invalid @enderror" id="category_id" name="category_id">
                                <option value="">{{ __('supportticket::tickets.select_category') }}</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('category_id')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="priority" class="form-label">{{ __('supportticket::tickets.priority') }}</label>
                            <select class="form-select @error('priority') is-invalid @enderror" id="priority" name="priority">
                                <option value="low" {{ old('priority') == 'low' ? 'selected' : '' }}>{{ __('supportticket::tickets.priority_low') }}</option>
                                <option value="medium" {{ old('priority', 'medium') == 'medium' ? 'selected' : '' }}>{{ __('supportticket::tickets.priority_medium') }}</option>
                                <option value="high" {{ old('priority') == 'high' ? 'selected' : '' }}>{{ __('supportticket::tickets.priority_high') }}</option>
                            </select>
                            @error('priority')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="message" class="form-label">{{ __('supportticket::tickets.message') }} <span class="text-danger">*</span></label>
                            <textarea class="form-control @error('message') is-invalid @enderror" id="message" name="message" rows="5" required>{{ old('message') }}</textarea>
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

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-send me-1"></i>{{ __('supportticket::tickets.submit_ticket') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 