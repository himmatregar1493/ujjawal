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

    .img-preview {
        max-width: 100%;
        height: auto;
        border-radius: 10px;
        margin-top: 10px;
    }
</style>

<div class="content-wrapper">
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="row">
            <div class="col-xl">
                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center pb-2 p-2 pt-3">
                        <h5 class="mb-0">University Add</h5>
                        <a href="{{Route('university.index')}}"><button id="create-university-btn" class="mb-0 btn btn-primary">Back</button></a>
                    </div>
                    <div class="card-body pb-2 p-2 pt-3">
                        @if (session('success'))
                            <div class="alert alert-success">
                                {{ session('success') }}
                            </div>
                        @endif
                        
                        @if (session('error'))
                            <div class="alert alert-danger">
                                {{ session('error') }}
                            </div>
                        @endif

                        <form id="apply-course-form" method="POST" action="{{ route('university.store') }}" enctype="multipart/form-data">
                            @csrf
                            <div class="row">
                                <div class="mt-1 mb-1 col-12 col-md-6 col-lg-4 col-xl-4">
                                    <label class="form-label required-label" for="university">University Name</label>
                                    <input type="text" name="name" class="form-control" placeholder="Enter university name...." value="{{ old('name') }}">
                                    @error('name')
                                        <div class="text-danger mt-1">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="mt-1 mb-1 col-12 col-md-6 col-lg-4 col-xl-4">
                                    <label class="form-label " for="urm_name">URM Name</label>
                                    <input type="text" name="urm_name" class="form-control" placeholder="Enter URM name...." value="{{ old('urm_name') }}">
                                    @error('urm_name')
                                        <div class="text-danger mt-1">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="mt-1 mb-1 col-12 col-md-6 col-lg-4 col-xl-4">
                                    <label class="form-label" for="urm_contact_no">URM Contact No</label>
                                    <input
                                        type="text"
                                        name="urm_contact_no"
                                        id="urm_contact_no"
                                        class="form-control"
                                        placeholder="Enter URM contact number...."
                                        value="{{ old('urm_contact_no') }}"
                                        pattern="\+?\d*"
                                        title="Only numbers and a single '+' at the beginning are allowed."
                                    >
                                    @error('urm_contact_no')
                                        <div class="text-danger mt-1">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mt-1 mb-1 col-12 col-md-6 col-lg-4 col-xl-4">
                                    <label class="form-label" for="logo">Logo</label>
                                    <input type="file" name="logo" class="form-control" accept="image/*" onchange="previewImage(event, 'logo-preview')">
                                    @error('logo')
                                        <div class="text-danger mt-1">{{ $message }}</div>
                                    @enderror
                                    <img id="logo-preview" class="img-preview">
                                </div>

                                <div class="mt-1 mb-1 col-12 col-md-6 col-lg-4 col-xl-4">
                                    <label class="form-label" for="banner">Banner</label>
                                    <input type="file" name="banner" class="form-control" accept="image/*" onchange="previewImage(event, 'banner-preview')">
                                    @error('banner')
                                        <div class="text-danger mt-1">{{ $message }}</div>
                                    @enderror
                                    <img id="banner-preview" class="img-preview">
                                </div>
                                
                                <div class="mt-1 mb-1 col-12 col-md-6 col-lg-4 col-xl-4">
                                    <label class="form-label required-label" for="status">University Status</label>
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
    function previewImage(event, previewId) {
        var reader = new FileReader();
        reader.onload = function(){
            var output = document.getElementById(previewId);
            output.src = reader.result;
            output.style.display = 'block';
        }
        reader.readAsDataURL(event.target.files[0]);
    }

    $(document).ready(function() {
    $('#urm_contact_no').on('input', function() {
        var value = $(this).val();
        // Remove all characters except digits and a single '+' at the start
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
