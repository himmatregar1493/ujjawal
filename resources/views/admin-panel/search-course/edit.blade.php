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
                        <h5 class="mb-0">University Edit</h5>
                        <a href="{{ route('university.index') }}">
                            <button id="create-university-btn" class="mb-0 btn btn-primary">Back</button>
                        </a>
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

                        <form id="apply-course-form" method="POST" action="{{ route('university.update', $university->id) }}" enctype="multipart/form-data">
                            @csrf
                            {{-- @method('PUT') --}}
                            <div class="row">
                                <div class="mt-1 mb-1 col-12 col-md-6 col-lg-4 col-xl-4">
                                    <label class="form-label required-label" for="university">University Name</label>
                                    <input type="text" name="name" id="university" class="form-control" placeholder="Enter university name...." value="{{ old('name', $university->name) }}">
                                    @error('name')
                                    <div class="text-danger mt-1">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mt-1 mb-1 col-12 col-md-6 col-lg-4 col-xl-4">
                                    <label class="form-label" for="urm_name">URM Name</label>
                                    <input type="text" name="urm_name" id="urm_name" class="form-control" placeholder="Enter URM name...." value="{{ old('urm_name', $university->urm_name) }}">
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
                                        value="{{ old('urm_contact_no', $university->urm_contact_no) }}"
                                        pattern="\+?\d*"
                                        title="Only numbers and a single '+' at the beginning are allowed."
                                    >
                                    @error('urm_contact_no')
                                    <div class="text-danger mt-1">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mt-1 mb-1 col-12 col-md-6 col-lg-4 col-xl-4">
                                    <label class="form-label" for="logo">Logo</label>
                                    <input type="file" name="logo" id="logo" class="form-control" accept="image/*" onchange="previewImage(event, 'logo-preview')">
                                    @error('logo')
                                    <div class="text-danger mt-1">{{ $message }}</div>
                                    @enderror
                                    <img id="logo-preview" class="img-preview" src="{{ asset('/admin_assets/images/university_image') }}/{{$university->logo}}">
                                </div>

                                <div class="mt-1 mb-1 col-12 col-md-6 col-lg-4 col-xl-4">
                                    <label class="form-label" for="banner">Banner</label>
                                    <input type="file" name="banner" id="banner" class="form-control" accept="image/*" onchange="previewImage(event, 'banner-preview')">
                                    @error('banner')
                                    <div class="text-danger mt-1">{{ $message }}</div>
                                    @enderror
                                    <img id="banner-preview" class="img-preview" src="{{ asset('/admin_assets/images/university_image') }}/{{$university->banner}}">
                                </div>

                                <div class="mt-1 mb-1 col-12 col-md-6 col-lg-4 col-xl-4">
                                    <label class="form-label required-label" for="status">University Status</label>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="status" name="status" value="1" {{ old('status', $university->status) ? 'checked' : '' }}>
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
        if (event.target.files[0]) {
            reader.readAsDataURL(event.target.files[0]);
        } else {
            document.getElementById(previewId).style.display = 'none';
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        const roleCheckboxes = document.querySelectorAll('.role-checkbox');
        const permissionCheckboxes = document.querySelectorAll('.permission-checkbox');

        function updateParentCheckbox() {
            roleCheckboxes.forEach(roleCheckbox => {
                const groupName = roleCheckbox.getAttribute('data-group');
                const associatedPermissions = Array.from(permissionCheckboxes).filter(checkbox => checkbox.getAttribute('data-group') === groupName);

                const allChecked = associatedPermissions.every(checkbox => checkbox.checked);
                const anyChecked = associatedPermissions.some(checkbox => checkbox.checked);

                roleCheckbox.checked = allChecked;
                roleCheckbox.indeterminate = !allChecked && anyChecked;
            });
        }

        roleCheckboxes.forEach(roleCheckbox => {
            roleCheckbox.addEventListener('change', function() {
                const isChecked = this.checked;
                const groupName = this.getAttribute('data-group');

                permissionCheckboxes.forEach(permissionCheckbox => {
                    if (permissionCheckbox.getAttribute('data-group') === groupName) {
                        permissionCheckbox.checked = isChecked;
                    }
                });

                updateParentCheckbox();
            });
        });

        permissionCheckboxes.forEach(permissionCheckbox => {
            permissionCheckbox.addEventListener('change', updateParentCheckbox);
        });

        updateParentCheckbox();
    });
</script>
@endsection
