@extends('layouts.admin.app')

@section('content')
<style>
    .custom-img {
        width: 100%;
        height: auto;
        object-fit: cover;
        border-radius: 20px;
    }

    .required-label::after {
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

    .permission-card {
        border-radius: 10px;
        border: 1px solid #ddd;
        overflow: hidden;
        margin-bottom: 1rem;
    }

    .permission-card-header {
        background-color: #f8f9fa;
        padding: 0.75rem 1.25rem;
        border-bottom: 1px solid #ddd;
        font-weight: bold;
    }

    .permission-card-body {
        padding: 1rem;
    }

    .permission-list-item {
        border-radius: 5px;
        margin-bottom: 0.5rem;
    }

    .permission-checkbox {
        margin-right: 0.5rem;
    }

    .file-preview {
        margin-top: 10px;
        width:100% !important;

        border-radius: 10px;
    }
    .img-preview {
        width: 100%;
        height:auto;
    }

    /* Styles for non-image previews */
    .pdf-preview,
    .doc-preview {
        width: 100%;
        height: 300px;
        border: 1px solid #ddd;
        border-radius: 10px;
    }
</style>

<div class="content-wrapper">
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="row">
            <div class="col-xl">
                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center pb-2 p-2 pt-3">
                        <h5 class="mb-0">visatool Edit</h5>
                        <a href="{{ route('visatool.index') }}">
                            <button id="create-visatool-btn" class="mb-0 btn btn-primary">Back</button>
                        </a>
                    </div>
                    <div class="card-body pb-2 p-2 pt-3">
                        <form id="apply-course-form" method="POST" action="{{ route('visatool.update', $visatool->id) }}" enctype="multipart/form-data">
                            @csrf
                            {{-- @method('PUT') --}}
                            <div class="row">
                                <!-- Visa Tool Name Input -->
                                <div class="mt-1 mb-1 col-12 col-md-6 col-lg-4 col-xl-4">
                                    <label class="form-label required-label" for="visatool">Visa Tool Name</label>
                                    <input type="text" name="name" id="visatool" class="form-control" placeholder="Enter visa tool name..." value="{{ old('name', $visatool->name) }}">
                                    @error('name')
                                    <div class="text-danger mt-1">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- File Input -->
                                <div class="mt-1 mb-1 col-12 col-md-6 col-lg-4 col-xl-4">
                                    <label class="form-label" for="file">File</label>
                                    <input type="file" name="file" id="file" class="form-control" accept=".jpg,.jpeg,.png,.gif,.bmp,.pdf,.doc,.docx" onchange="previewFile(event)">
                                    @error('file')
                                    <div class="text-danger mt-1">{{ $message }}</div>
                                    @enderror

                                    <!-- File Preview -->
                                    <div id="file-preview" class="file-preview mt-3">
                                        @if(pathinfo($visatool->link, PATHINFO_EXTENSION) === 'pdf')
                                            <embed src="{{ asset('admin_assets/images/visatool_image/' . $visatool->link) }}" class="pdf-preview" type="application/pdf">
                                        @elseif(in_array(pathinfo($visatool->link, PATHINFO_EXTENSION), ['doc', 'docx']))
                                            <div class="doc-preview">
                                                <a href="{{ asset('admin_assets/images/visatool_image/' . $visatool->link) }}" target="_blank" class="btn btn-secondary">View Document</a>
                                            </div>
                                        @else
                                            <img src="{{ asset('admin_assets/images/visatool_image/' . $visatool->link) }}" class="img-preview">
                                        @endif
                                    </div>
                                </div>

                                <!-- Status Switch -->
                                <div class="mt-1 mb-1 col-12 col-md-6 col-lg-4 col-xl-4">
                                    <label class="form-label required-label" for="status">Visa Tool Status</label>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="status" name="status" value="1" {{ old('status', $visatool->is_active) ? 'checked' : '' }}>
                                    </div>
                                    @error('status')
                                    <div class="text-danger mt-1">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Save Button -->
                                <div class="mt-2 mb-1 col-12">
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

<script>
    function previewFile(event) {
        const fileInput = event.target;
        const file = fileInput.files[0];
        const previewElement = document.getElementById('file-preview');
        const fileReader = new FileReader();
        
        previewElement.innerHTML = ''; // Clear any existing preview
        
        if (file) {
            const fileType = file.type;

            if (fileType.startsWith('image/')) {
                // Show image preview
                fileReader.onload = function() {
                    const imgElement = document.createElement('img');
                    imgElement.src = fileReader.result;
                    imgElement.className = 'img-preview';
                    previewElement.appendChild(imgElement);
                };
                fileReader.readAsDataURL(file);
            } else if (fileType === 'application/pdf') {
                // Show PDF preview
                const embedElement = document.createElement('embed');
                embedElement.src = URL.createObjectURL(file);
                embedElement.className = 'pdf-preview';
                embedElement.type = 'application/pdf';
                previewElement.appendChild(embedElement);
            } else if (fileType === 'application/msword' || fileType === 'application/vnd.openxmlformats-officedocument.wordprocessingml.document') {
                // Show link for Word documents
                const docLink = document.createElement('a');
                docLink.href = URL.createObjectURL(file);
                docLink.target = '_blank';
                docLink.className = 'btn btn-secondary';
                docLink.textContent = 'View Document';
                previewElement.appendChild(docLink);
            } else {
                previewElement.textContent = 'Unsupported file format';
            }
        }
    }
</script>
@endsection
