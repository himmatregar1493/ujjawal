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
        width: 100% !important;
        border-radius: 10px;
    }

    .img-preview {
        width: 100%;
        height: auto;
    }

    /* Styles for non-image previews */
    .pdf-preview,
    .doc-preview,
    .ppt-preview {
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
                        <h5 class="mb-0">University Presentation Edit</h5>
                        <a href="{{ route('university-presentation.index') }}">
                            <button id="create-UniversityPresentation-btn" class="mb-0 btn btn-primary">Back</button>
                        </a>
                    </div>
                    <div class="card-body pb-2 p-2 pt-3">
                        <form id="apply-course-form" method="POST" action="{{ route('university-presentation.update', $UniversityPresentation->id) }}" enctype="multipart/form-data">
                            @csrf
                            {{-- @method('PUT') --}}
                            <div class="row">
                                <!-- Refund Request Name Input -->
                                <div class="mt-1 mb-1 col-12 col-md-6 col-lg-4 col-xl-4">
                                    <label class="form-label required-label" for="UniversityPresentation">Refund Request Name</label>
                                    <input type="text" name="name" id="UniversityPresentation" class="form-control" placeholder="Enter Refund Request name..." value="{{ old('name', $UniversityPresentation->name) }}">
                                    @error('name')
                                    <div class="text-danger mt-1">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- University Selection -->
                                <div class="mt-1 mb-1 col-12 col-md-6 col-lg-4 col-xl-4">
                                    <label class="form-label required-label" for="university_id">University</label>
                                    <select class="form-control select2" id="university_id" name="university_id">
                                        <option value="">Select University</option>
                                        @foreach($universities as $university)
                                        <option value="{{ $university['id'] }}" {{ old('university_id',$UniversityPresentation['university_id']) == $university['id'] ? 'selected' : '' }}>{{ $university['name'] }}</option>
                                        @endforeach
                                    </select>
                                    @error('university_id')
                                    <div class="text-danger mt-1">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- File Input -->
                                <div class="mt-1 mb-1 col-12 col-md-6 col-lg-4 col-xl-4">
                                    <label class="form-label" for="file">File</label>
                                    <input type="file" name="file" id="file" class="form-control" accept=".jpg,.jpeg,.png,.gif,.bmp,.pdf,.doc,.docx,.ppt,.pptx" onchange="previewFile(event)">
                                    @error('file')
                                    <div class="text-danger mt-1">{{ $message }}</div>
                                    @enderror

                                    <!-- File Preview -->
                                    <div id="file-preview" class="file-preview mt-3">
                                        @if(in_array(pathinfo($UniversityPresentation->link, PATHINFO_EXTENSION), ['jpg', 'jpeg', 'png', 'gif', 'bmp']))
                                            <!-- Image Preview -->
                                            <img src="{{ asset('admin_assets/images/UniversityPresentation_image/' . $UniversityPresentation->link) }}" class="img-preview">
                                        @elseif(pathinfo($UniversityPresentation->link, PATHINFO_EXTENSION) === 'pdf')
                                            <!-- PDF Preview -->
                                            <iframe src="{{ asset('admin_assets/images/UniversityPresentation_image/' . $UniversityPresentation->link) }}" class="pdf-preview" frameborder="0"></iframe>
                                        @elseif(in_array(pathinfo($UniversityPresentation->link, PATHINFO_EXTENSION), ['doc', 'docx']))
                                            <!-- Office Document Preview (Word) -->
                                            <iframe src="https://view.officeapps.live.com/op/embed.aspx?src={{ asset('admin_assets/images/UniversityPresentation_image/' . $UniversityPresentation->link) }}" class="doc-preview" frameborder="0"></iframe>
                                        @elseif(in_array(pathinfo($UniversityPresentation->link, PATHINFO_EXTENSION), ['ppt', 'pptx']))
                                            <!-- PowerPoint Document Preview -->
                                            <iframe src="https://view.officeapps.live.com/op/embed.aspx?src={{ asset('admin_assets/images/UniversityPresentation_image/' . $UniversityPresentation->link) }}" class="ppt-preview" frameborder="0"></iframe>
                                        @else
                                            <!-- Unsupported Format -->
                                            <div class="alert alert-warning">Unsupported file format. Please download to view the document.</div>
                                        @endif
                                    </div>
                                </div>

                                <!-- Status Switch -->
                                <div class="mt-1 mb-1 col-12 col-md-6 col-lg-4 col-xl-4">
                                    <label class="form-label required-label" for="status">Refund Request Status</label>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="status" name="status" value="1" {{ old('status', $UniversityPresentation->is_active) ? 'checked' : '' }}>
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
        
        previewElement.innerHTML = ''; // Clear any existing preview

        if (file) {
            const fileType = file.type;
            const fileUrl = URL.createObjectURL(file); // This is for temporary preview

            if (fileType.startsWith('image/')) {
                // Show image preview
                const imgElement = document.createElement('img');
                imgElement.src = fileUrl;
                imgElement.className = 'img-preview';
                previewElement.appendChild(imgElement);
            } else if (fileType === 'application/pdf') {
                // Show PDF preview
                const embedElement = document.createElement('embed');
                embedElement.src = fileUrl;
                embedElement.className = 'pdf-preview';
                embedElement.type = 'application/pdf';
                previewElement.appendChild(embedElement);
            } else if (fileType === 'application/msword' || fileType === 'application/vnd.openxmlformats-officedocument.wordprocessingml.document') {
                // Temporarily show Word document link, replace with public URL after upload
                const iframeElement = document.createElement('iframe');
                iframeElement.src = `https://view.officeapps.live.com/op/embed.aspx?src=${fileUrl}`;
                iframeElement.className = 'doc-preview';
                iframeElement.frameBorder = '0';
                previewElement.appendChild(iframeElement);
            } else if (fileType === 'application/vnd.ms-powerpoint' || fileType === 'application/vnd.openxmlformats-officedocument.presentationml.presentation') {
                // Temporarily show PowerPoint link, replace with public URL after upload
                const iframeElement = document.createElement('iframe');
                iframeElement.src = `https://view.officeapps.live.com/op/embed.aspx?src=${fileUrl}`;
                iframeElement.className = 'ppt-preview';
                iframeElement.frameBorder = '0';
                previewElement.appendChild(iframeElement);
            } else {
                previewElement.innerHTML = '<div class="alert alert-warning">Unsupported file format. Please download to view the document.</div>';
            }
        }
    }


</script>
@endsection
