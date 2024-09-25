@extends('layouts.admin.app')

@section('content')
<style>
    .custom-img {
        width: 100%;
        max-height: 200px;
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

    #image-container {
        position: relative;
        display: inline-block;
    }

      #image-container {
        position: relative;
        display: inline-block;
    }

    #replace-button {
        display: none;
        z-index: 1000;
        position: relative;
        top:-67px;
    }

     #image-container:hover #replace-button {
        display: inherit;
    }
    #image-preview {
        max-width: 100%;
        max-height: 100px;
    }
</style>

<div class="content-wrapper">
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="row">
            <div class="col-xl">
                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center pb-2 p-2 pt-3">
                        <h5 class="mb-0">User Create</h5>
                        <a href="{{Route('user.index')}}">
                            <button id="create-user-btn" class="mb-0 btn btn-primary">Back</button>
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

                        <form id="apply-course-form" method="POST" action="{{ route('user.store') }}" enctype="multipart/form-data" >
                            @csrf
                            <div class="row">

                                <div class="mt-1 mb-1 col-12 col-md-6 col-lg-4 col-xl-4">
                                    <label class="form-label required-label" for="company">Select Company Name</label>
                                    <select class="form-control select2" id="company"  name="company">
                                        <option value="">Select Company</option>
                                        @foreach($companies as $key => $value)
                                            <option value="{{ $value['id'] }}" {{ $value['id'] = old('company')? 'selected' : '' }}>{{ $value['name'] }}</option>
                                        @endforeach
                                    </select>
                                    <div class="text-danger mt-1" id="company-error"></div>
                                    @error('company')
                                        <div class="text-danger mt-1">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="mt-1 mb-1 col-12 col-md-6 col-lg-4 col-xl-4">
                                    <label class="form-label required-label" for="file">Profile Picture</label>
                                    <br>
                                    <input type="file" name="file" id="file" class="form-control" accept="image/*">
                                    <div id="image-container" class="d-none">
                                        <img id="image-preview" src="#" alt="Image Preview" class="img-thumbnail" />

                                    </div>
                                    <div class="text-danger mt-1" id="file-error"></div>
                                    @error('file')
                                        <div class="text-danger mt-1">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="mt-1 mb-1 col-12 col-md-6 col-lg-4 col-xl-4">
                                    <label class="form-label required-label" for="name">Name</label>
                                    <input type="text" name="name" id="name" class="form-control" placeholder="Enter Name..." value="{{ old('name') }}">
                                    <div class="text-danger mt-1" id="name-error"></div>
                                    @error('name')
                                        <div class="text-danger mt-1">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="mt-1 mb-1 col-12 col-md-6 col-lg-4 col-xl-4">
                                    <label class="form-label required-label" for="email">Email</label>
                                    <input type="text" name="email" id="email" autocomplete="off"  class="form-control" placeholder="Enter email..." value="{{ old('email') }}">
                                    <div class="text-danger mt-1" id="email-error"></div>
                                    @error('email')
                                        <div class="text-danger mt-1">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="mt-1 mb-1 col-12 col-md-6 col-lg-4 col-xl-4">
    <label class="form-label required-label" for="password">Password</label>
    <input type="password" name="password" id="password" autocomplete="off" class="form-control" placeholder="Enter password..."  value="">
    <div class="text-danger mt-1" id="password-error"></div>
    @error('password')
        <div class="text-danger mt-1">{{ $message }}</div>
    @enderror
</div>

<div class="mt-1 mb-1 col-12 col-md-6 col-lg-4 col-xl-4">
    <label class="form-label required-label" for="password_confirmation">Confirm Password</label>
    <input type="password" name="password_confirmation" id="password_confirmation" autocomplete="off" class="form-control" placeholder="Enter confirm password..." value="">
    <div class="text-danger mt-1" id="password_confirmation-error"></div>
    @error('password_confirmation')
        <div class="text-danger mt-1">{{ $message }}</div>
    @enderror
</div>
                                <hr class="mt-3">
                                <div class="mt-1 mb-1 col-12 col-md-6 col-lg-4 col-xl-4">
                                    <label class="form-label required-label" for="intake">Roles</label>
                                    <select class="form-control select2" id="selectRole" multiple name="role[]">
                                        @foreach($roles as $key => $value)
                                            <option value="{{ $value['id'] }}" {{ in_array($value['id'], old('role', [])) ? 'selected' : '' }}>{{ $value['name'] }}</option>
                                        @endforeach
                                    </select>
                                    <div class="text-danger mt-1" id="role-error"></div>
                                    @error('role')
                                        <div class="text-danger mt-1">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div id="update_permission" class="mt-4"></div>

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
document.addEventListener('DOMContentLoaded', function () {
    const fileInput = document.getElementById('file');
    const imageContainer = document.getElementById('image-container');
    const imagePreview = document.getElementById('image-preview');
    const replaceButton = document.getElementById('replace-button');

    fileInput.addEventListener('change', function (event) {
        const file = event.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function (e) {
                imagePreview.src = e.target.result;
                imageContainer.classList.remove('d-none');

            };
            reader.readAsDataURL(file);
        }
    });

    replaceButton.addEventListener('click', function () {
        fileInput.classList.remove('d-none');
        imageContainer.classList.add('d-none');
        fileInput.value = ''; // Clear the file input
    });
});

$(document).ready(function() {
    $('.select2').select2();

getPermission();
    function getPermission() {
        var roles = $('#selectRole').val();
        $.ajax({
            url: '{{ route('permission.get_user_permission') }}',
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                roles: roles
            },
            success: function(response) {
                $('#update_permission').html(response.html);
            },
            error: function() {
                $('#update_permission').html(`
                    <div class="w-100 d-flex">
                        Error occurred. Please try again.
                    </div>
                `);
            }
        });
    }

    $('#selectRole').on('change', function() {
       getPermission();

    });

    function updateParentCheckbox() {
        $('.role-checkbox').each(function() {
            const groupName = $(this).data('group');
            const associatedPermissions = $(`.permission-checkbox[data-group="${groupName}"]`);

            const allChecked = associatedPermissions.length > 0 && associatedPermissions.length === associatedPermissions.filter(':checked').length;
            const anyChecked = associatedPermissions.is(':checked');

            $(this).prop('checked', allChecked).prop('indeterminate', !allChecked && anyChecked);
        });
    }

    $(document).on('change', '.role-checkbox', function () {
        const isChecked = $(this).is(':checked');
        const groupName = $(this).data('group');

        $(`.permission-checkbox[data-group="${groupName}"]`).prop('checked', isChecked);

        updateParentCheckbox();
    });

    $(document).on('change', '.permission-checkbox', function () {
        updateParentCheckbox();
    });

    updateParentCheckbox();

    // Client-side validation
    $('#apply-course-form').on('submit', function(e) {
        e.preventDefault();
        let isValid = true;

        // Clear previous errors
        $('.text-danger').text('');

        const file = $('#file').val();
        const email = $('#email').val();
        const password = $('#password').val();
        const conformPassword = $('#password_confirmation').val();
        const roles = $('#selectRole').val();
        const company = $('#company').val();

        if (!file) {
            $('#file-error').text('Profile Picture is required.');
            isValid = false;
        }

        if (!email) {
            $('#email-error').text('Email is required.');
            isValid = false;
        } else if (!validateEmail(email)) {
            $('#email-error').text('Email is not valid.');
            isValid = false;
        }

        if (!password) {
            $('#password-error').text('Password is required.');
            isValid = false;
        }

        if (!conformPassword) {
            $('#password_confirmation-error').text('Confirm Password is required.');
            isValid = false;
        } else if (password !== conformPassword) {
            $('#password_confirmation-error').text('Passwords do not match.');
            isValid = false;
        }

        if (!roles || roles.length === 0) {
            $('#role-error').text('At least one role is required.');
            isValid = false;
        }

        if (!company) {
            $('#company-error').text('Select Company');
            isValid = false;
        }

        if (isValid) {
            this.submit();
        }
    });

    function validateEmail(email) {
        const re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@(([^<>()[\]\.,;:\s@"]+\.[^<>()[\]\.,;:\s@"]{2,}))$/i;
        return re.test(String(email).toLowerCase());
    }
});
</script>
@endsection
