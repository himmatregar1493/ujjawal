@extends('layouts.admin.app')

@section('content')
<style>
    .custom-img {
        width: 100%;
        height: auto;
        object-fit: cover;
        border-radius: 20px;
    }

    .-label::after {
        content: '*';
        color: red;
        margin-left: 5px;
    }

    .loader-image-design {
        width: 50px;
        justify-content: center;
        margin: auto;
    }

    #detail-page {
        justify-content: center;
    }

    .img-preview, .file-preview {
        max-width: 100%;
        height: auto;
        border-radius: 10px;
        margin-top: 10px;
        display: none;
    }

    .file-info {
        margin-top: 10px;
    }
</style>

<div class="content-wrapper">
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="row">
            <div class="col-xl">
                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center pb-2 p-2 pt-3">
                        <h5 class="mb-0">Notice Create</h5>
                        <a href="{{ route('entry-requirement.index') }}">
                            <button id="create-university-btn" class="mb-0 btn btn-primary">Back</button>
                        </a>
                    </div>
                    <div class="card-body pb-2 p-2 pt-3">
                        <form id="apply-course-form" method="POST" action="{{ route('entry-requirement.store') }}" enctype="multipart/form-data">
                            @csrf
                            <div class="row">
                                <!-- Description Field -->
                                <div class="mt-1 mb-1 col-12 col-md-12 col-lg-12 col-xl-12">
                                    <label class="form-label" for="description">Description</label>
                                    <textarea name="description" class="form-control" placeholder="Enter description details...">{{ old('description') }}</textarea>
                                    @error('description')
                                        <div class="text-danger mt-1">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Status Field -->
                                <div class="mt-1 mb-1 col-12 col-md-6 col-lg-4 col-xl-4">
                                    <label class="form-label" for="status">Entry Requirement Status</label>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="status" name="status" value="1" {{ old('status') ? 'checked' : '' }}>
                                    </div>
                                    @error('status')
                                        <div class="text-danger mt-1">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Save Button -->
                                <div class="mt-2 mb-1 col-12 col-md-12 col-lg-12 col-xl-12">
                                    <button type="submit" class="btn btn-primary mt-1">Save</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- TinyMCE CDN -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/tinymce/7.3.0/tinymce.min.js" integrity="sha512-RUZ2d69UiTI+LdjfDCxqJh5HfjmOcouct56utQNVRjr90Ea8uHQa+gCxvxDTC9fFvIGP+t4TDDJWNTRV48tBpQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        var csrfToken = "{{ csrf_token() }}";

        tinymce.init({
            selector: 'textarea[name="description"]',
            plugins: 'advlist autolink lists link image charmap preview anchor textcolor code',
            toolbar: 'undo redo | formatselect | bold italic underline strikethrough | alignleft aligncenter alignright | bullist numlist outdent indent | removeformat | code | image',
            height: 300,
            content_css: 'https://www.yourwebsite.com/path/to/custom.css',
            menubar: false,
            setup: function(editor) {
                editor.on('init', function() {
                    // Optional: Customize the formatselect options
                });
            },
            formats: {
                paragraph: { block: 'p' },
                heading1: { block: 'h1', classes: 'heading1' },
                heading2: { block: 'h2', classes: 'heading2' },
                heading3: { block: 'h3', classes: 'heading3' },
                heading4: { block: 'h4', classes: 'heading4' },
                heading5: { block: 'h5', classes: 'heading5' },
                heading6: { block: 'h6', classes: 'heading6' },
                blockquote: { block: 'blockquote' },
                code: { block: 'code' },
                pre: { block: 'pre' }
            },
            images_upload_url: '/upload-image',
            images_upload_handler: function(blobInfo, success, failure) {
    var xhr = new XMLHttpRequest();
    var formData = new FormData();

    xhr.open('POST', '/upload-image', true);
    xhr.setRequestHeader('X-CSRF-TOKEN', csrfToken);

    xhr.onload = function() {
        if (xhr.status === 200) {
            try {
                var json = JSON.parse(xhr.responseText);
                console.log('Response JSON:', json); // Debug output
                if (json && typeof json.location === 'string') {
                    console.log('Image URL:', json.location); // Debug output
                    success(json.location); // Pass the URL to TinyMCE
                } else {
                    failure('Invalid JSON: ' + xhr.responseText);
                }
            } catch (e) {
                failure('Invalid JSON: ' + xhr.responseText);
            }
        } else {
            failure('HTTP Error: ' + xhr.status);
        }
    };

    xhr.onerror = function() {
        failure('Network Error');
    };

    formData.append('file', blobInfo.blob(), blobInfo.filename());
    xhr.send(formData);
}

        });
    });
</script>

@endsection
