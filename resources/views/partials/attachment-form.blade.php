<div class="card mb-4">
    <div class="card-header">
        <i class="bi bi-paperclip me-1"></i>{{ __('supportticket::attachments.attachments') }}
    </div>
    <div class="card-body">
        <form id="attachment-upload-form" action="{{ route('supportticket.attachments.store') }}" method="POST" enctype="multipart/form-data" class="dropzone">
            @csrf
            <input type="hidden" name="ticketable_id" value="{{ $ticketable->id }}">
            <input type="hidden" name="ticketable_type" value="{{ get_class($ticketable) }}">
            
            <div class="dz-message">
                <div class="mb-3">
                    <i class="bi bi-cloud-arrow-up display-4"></i>
                </div>
                <h5>{{ __('supportticket::attachments.drop_files') }}</h5>
                <p class="text-muted">{{ __('supportticket::attachments.max_file_size', ['size' => config('supportticket.attachments.max_size', 5)]) }}</p>
            </div>
        </form>
        
        <div id="attachment-preview" class="mt-3">
            @if($ticketable->attachments->isNotEmpty())
                <div class="d-flex flex-wrap">
                    @foreach($ticketable->attachments as $attachment)
                        <div class="attachment-item me-3 mb-3 position-relative" id="attachment-{{ $attachment->id }}">
                            <div class="card" style="width: 120px;">
                                <div class="card-img-top text-center pt-3">
                                    @if(in_array($attachment->extension, ['jpg', 'jpeg', 'png', 'gif']))
                                        <img src="{{ route('supportticket.attachments.download', $attachment) }}" class="img-thumbnail" style="height: 60px; object-fit: cover;">
                                    @else
                                        <i class="bi bi-file-earmark-{{ $attachment->icon }} display-4"></i>
                                    @endif
                                </div>
                                <div class="card-body p-2">
                                    <p class="card-text small text-truncate" title="{{ $attachment->original_name }}">
                                        {{ $attachment->original_name }}
                                    </p>
                                    <div class="btn-group btn-group-sm w-100">
                                        <a href="{{ route('supportticket.attachments.download', $attachment) }}" class="btn btn-outline-primary">
                                            <i class="bi bi-download"></i>
                                        </a>
                                        @can('delete', $attachment)
                                            <button type="button" class="btn btn-outline-danger delete-attachment" data-attachment-id="{{ $attachment->id }}">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        @endcan
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-muted text-center">{{ __('supportticket::attachments.no_attachments') }}</p>
            @endif
        </div>
    </div>
</div>

@push('styles')
    <link href="https://unpkg.com/dropzone@5/dist/min/dropzone.min.css" rel="stylesheet" type="text/css" />
    <style>
        .dropzone {
            border: 2px dashed #0087F7;
            border-radius: 5px;
            background: #F8F9FA;
        }
        .attachment-item .card {
            transition: all 0.2s ease;
        }
        .attachment-item .card:hover {
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
        }
    </style>
@endpush

@push('scripts')
    <script src="https://unpkg.com/dropzone@5/dist/min/dropzone.min.js"></script>
    <script>
        Dropzone.autoDiscover = false;
        
        document.addEventListener('DOMContentLoaded', function() {
            var myDropzone = new Dropzone("#attachment-upload-form", {
                parallelUploads: 3,
                maxFilesize: {{ config('supportticket.attachments.max_size', 5) }},
                acceptedFiles: "{{ config('supportticket.attachments.allowed_types', '.jpg,.jpeg,.png,.gif,.pdf,.doc,.docx,.xls,.xlsx,.zip,.rar') }}",
                addRemoveLinks: true,
                dictRemoveFile: "{{ __('supportticket::attachments.remove') }}",
                dictCancelUpload: "{{ __('supportticket::attachments.cancel') }}",
                success: function(file, response) {
                    // Dosya başarıyla yüklendi, sayfayı yenile
                    location.reload();
                }
            });
            
            // Dosya silme işlemi
            document.querySelectorAll('.delete-attachment').forEach(function(button) {
                button.addEventListener('click', function() {
                    if (confirm("{{ __('supportticket::attachments.confirm_delete') }}")) {
                        var attachmentId = this.getAttribute('data-attachment-id');
                        
                        fetch("{{ route('supportticket.attachments.destroy', '') }}/" + attachmentId, {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                                'Accept': 'application/json',
                                'Content-Type': 'application/json'
                            }
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                document.getElementById('attachment-' + attachmentId).remove();
                            }
                        });
                    }
                });
            });
        });
    </script>
@endpush 