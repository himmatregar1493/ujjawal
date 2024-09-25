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
                        <h5 class="mb-0">Entry Requirement Edit</h5>
                        <a href="{{ route('entry-requirement.index') }}">
                            <button id="create-visatool-btn" class="mb-0 btn btn-primary">Back</button>
                        </a>
                    </div>
                    <div class="card-body pb-2 p-2 pt-3">
                        <form id="apply-course-form" method="POST" action="{{ route('entry-requirement.update', $EntryRequirement->id) }}" enctype="multipart/form-data">
                            @csrf
                           
                            <div class="row">
                                <div class="mt-1 mb-1 col-12 col-md-6 col-lg-4 col-xl-4">
                                    <label class="form-label -label" for="university_id">University</label>
                                    <input type="text" name="university_id" value="{{$EntryRequirement->university_id}}" hidden > 
                                    <input type="text" class="form-control" value="{{$EntryRequirement->university_name}}">
                                </div>

                                <div class="mt-1 mb-1 col-12 col-md-6 col-lg-4 col-xl-4">
                                    <label class="form-label" for="english_requirement">English Requirement</label>
                                    <textarea name="english_requirement" class="form-control" placeholder="Enter English requirements..." >{{ old('english_requirement', $EntryRequirement->english_requirement) }}</textarea>
                                    @error('english_requirement')
                                        <div class="text-danger mt-1">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mt-1 mb-1 col-12 col-md-6 col-lg-4 col-xl-4">
                                    <label class="form-label" for="academic_requirement">Academic Requirement</label>
                                    <textarea name="academic_requirement" class="form-control" placeholder="Enter academic requirements..." >{{ old('academic_requirement', $EntryRequirement->academic_requirement) }}</textarea>
                                    @error('academic_requirement')
                                        <div class="text-danger mt-1">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mt-1 mb-1 col-12 col-md-6 col-lg-4 col-xl-4">
                                    <label class="form-label" for="offer_timeline">Offer Timeline</label>
                                    <textarea name="offer_timeline" class="form-control" placeholder="Enter offer timeline..." >{{ old('offer_timeline', $EntryRequirement->offer_timeline) }}</textarea>
                                    @error('offer_timeline')
                                        <div class="text-danger mt-1">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mt-1 mb-1 col-12 col-md-6 col-lg-4 col-xl-4">
                                    <label class="form-label" for="credibility">Credibility</label>
                                    <select name="credibility" class="form-control">
                                        <option value="">Select Credibility</option>
                                        <option value="Yes" {{ old('credibility', $EntryRequirement->credibility) == 'Yes' ? 'selected' : '' }}>Yes</option>
                                        <option value="No" {{ old('credibility', $EntryRequirement->credibility) == 'No' ? 'selected' : '' }}>No</option>
                                    </select>
                                    @error('credibility')
                                        <div class="text-danger mt-1">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mt-1 mb-1 col-12 col-md-6 col-lg-4 col-xl-4">
                                    <label class="form-label" for="type">TYPE</label>
                                    <select name="type" class="form-control">
                                        <option value="">Select Type</option>
                                        <option value="PG" {{ old('type', $EntryRequirement->type) == 'PG' ? 'selected' : '' }}>PG</option>
                                        <option value="UG" {{ old('type', $EntryRequirement->type) == 'UG' ? 'selected' : '' }}>UG</option>
                                    </select>
                                    @error('type')
                                        <div class="text-danger mt-1">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mt-1 mb-1 col-12 col-md-6 col-lg-4 col-xl-4">
                                    <label class="form-label" for="finance">Finance</label>
                                    <textarea name="finance" class="form-control" placeholder="Enter finance details..." >{{ old('finance', $EntryRequirement->finance) }}</textarea>
                                    @error('finance')
                                        <div class="text-danger mt-1">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mt-1 mb-1 col-12 col-md-6 col-lg-4 col-xl-4">
                                    <label class="form-label" for="status">Entry Requirement Status</label>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="status" name="status" value="1" {{ old('status', $EntryRequirement->is_active) ? 'checked' : '' }}>
                                    </div>
                                    @error('status')
                                        <div class="text-danger mt-1">{{ $message }}</div>
                                    @enderror
                                </div>

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
