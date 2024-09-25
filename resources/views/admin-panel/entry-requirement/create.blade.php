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
                        <h5 class="mb-0">Entry Requirements Create</h5>
                        <a href="{{ route('entry-requirement.index') }}">
                            <button id="create-university-btn" class="mb-0 btn btn-primary">Back</button>
                        </a>
                    </div>
                    <div class="card-body pb-2 p-2 pt-3">
                        <form id="apply-course-form" method="POST" action="{{ route('entry-requirement.store') }}" enctype="multipart/form-data">
                            @csrf
                            <div class="row">
                                <div class="mt-1 mb-1 col-12 col-md-6 col-lg-4 col-xl-4">
                                    <label class="form-label -label" for="university_id">University</label>
                                    <select class="form-control select2" id="university_id" name="university_id" >
                                        <option value="">Select University</option>
                                        @foreach($universities as $university)
                                            <option value="{{ $university['id'] }}" {{ old('university_id') == $university['id'] ? 'selected' : '' }}>{{ $university['name'] }}</option>
                                        @endforeach
                                    </select>
                                    @error('university_id')
                                        <div class="text-danger mt-1">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mt-1 mb-1 col-12 col-md-6 col-lg-4 col-xl-4">
                                    <label class="form-label" for="english_requirement">English Requirement</label>
                                    <textarea name="english_requirement" class="form-control" placeholder="Enter English requirements..." >{{ old('english_requirement') }}</textarea>
                                    @error('english_requirement')
                                        <div class="text-danger mt-1">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mt-1 mb-1 col-12 col-md-6 col-lg-4 col-xl-4">
                                    <label class="form-label" for="academic_requirement">Academic Requirement</label>
                                    <textarea name="academic_requirement" class="form-control" placeholder="Enter academic requirements..." >{{ old('academic_requirement') }}</textarea>
                                    @error('academic_requirement')
                                        <div class="text-danger mt-1">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mt-1 mb-1 col-12 col-md-6 col-lg-4 col-xl-4">
                                    <label class="form-label" for="offer_timeline">Offer Timeline</label>
                                    <textarea name="offer_timeline" class="form-control" placeholder="Enter offer timeline..." >{{ old('offer_timeline') }}</textarea>
                                    @error('offer_timeline')
                                        <div class="text-danger mt-1">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mt-1 mb-1 col-12 col-md-6 col-lg-4 col-xl-4">
                                    <label class="form-label" for="credibility">Credibility</label>
                                    <select name="credibility" class="form-control">
                                        <option value="">Select Credibility</option>
                                        <option value="Yes" {{ old('credibility') == 'Yes' ? 'selected' : '' }}>Yes</option>
                                        <option value="No" {{ old('credibility') == 'No' ? 'selected' : '' }}>No</option>
                                    </select>
                                    @error('credibility')
                                        <div class="text-danger mt-1">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mt-1 mb-1 col-12 col-md-6 col-lg-4 col-xl-4">
                                    <label class="form-label" for="type">Type</label>
                                    <select name="type" class="form-control">
                                        <option value="">Select type</option>
                                        <option value="PG" {{ old('type') == 'PG' ? 'selected' : '' }}>PG</option>
                                        <option value="UG" {{ old('type') == 'UG' ? 'selected' : '' }}>UG</option>
                                    </select>
                                    @error('type')
                                        <div class="text-danger mt-1">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mt-1 mb-1 col-12 col-md-6 col-lg-4 col-xl-4">
                                    <label class="form-label" for="finance">Finance</label>
                                    <textarea name="finance" class="form-control" placeholder="Enter finance details..." >{{ old('finance') }}</textarea>
                                    @error('finance')
                                        <div class="text-danger mt-1">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mt-1 mb-1 col-12 col-md-6 col-lg-4 col-xl-4">
                                    <label class="form-label" for="status">Entry Requirement Status</label>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="status" name="status" value="1" {{ old('status') ? 'checked' : '' }}>
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
    function previewFile(event, previewId, infoId) {
        var file = event.target.files[0];
        var preview = document.getElementById(previewId);
        var info = document.getElementById(infoId);

        if (file) {
            var reader = new FileReader();

            reader.onload = function(e) {
                if (file.type.startsWith('image/')) {
                    preview.src = e.target.result;
                    preview.style.display = 'block';
                    info.style.display = 'none';
                } else {
                    preview.style.display = 'none';
                    info.textContent = `Selected file: ${file.name}`;
                    info.style.display = 'block';
                }
            };

            reader.readAsDataURL(file);
        }
    }

    $(document).ready(function() {
        $('#urm_contact_no').on('input', function() {
            var value = $(this).val();
            var cleanedValue = value.replace(/[^0-9+]/g, ''); // Remove invalid characters
            if (cleanedValue.startsWith('+')) {
                cleanedValue = '+' + cleanedValue.replace(/[^0-9]/g, '');
            } else {
                cleanedValue = cleanedValue.replace(/[^0-9]/g, '');
            }
            $(this).val(cleanedValue);
        });
    });
</script>
@endsection
