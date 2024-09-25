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
</style>

<div class="content-wrapper">
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="row">
            <div class="col-xl">
                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center pb-2 p-2 pt-3">
                        <h5 class="mb-0">Vendor Create</h5>
                        <a href="{{Route('vendor.index')}}"><button id="create-Vendor-btn" class="mb-0 btn btn-primary">Back</button></a>
                    </div>
                    <div class="card-body pb-2 p-2 pt-3">


                        <form id="apply-course-form" method="POST" action="{{ route('vendor.store') }}">
                            @csrf
                            <div class="row">
                                <div class="mt-1 mb-1 col-12 col-md-6 col-lg-4 col-xl-4">
                                    <label class="form-label required-label" for="Vendor-name">Vendor Name</label>
                                    <input type="text" id="Vendor-name" name="name" class="form-control" placeholder="Enter Vendor name...." value="{{ old('name') }}">
                                    @error('name')
                                        <div class="text-danger mt-1">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mt-1 mb-1 col-12 col-md-6 col-lg-4 col-xl-4">
                                    <label class="form-label " for="Vendor-email">Vendor Email</label>
                                    <input type="email" id="Vendor-email" name="email" class="form-control" placeholder="Enter Vendor email...." value="{{ old('email') }}">
                                    @error('email')
                                        <div class="text-danger mt-1">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mt-1 mb-1 col-12 col-md-6 col-lg-4 col-xl-4">
                                    <label class="form-label" for="Vendor-phone">Vendor Phone</label>
                                    <input type="text" id="Vendor-phone" name="phone" class="form-control" placeholder="Enter Vendor phone...." value="{{ old('phone') }}">
                                    @error('phone')
                                        <div class="text-danger mt-1">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mt-1 mb-1 col-12 col-md-6 col-lg-4 col-xl-4">
                                    <label class="form-label " for="Vendor-alt-phone">Vendor Alternative Phone</label>
                                    <input type="text" id="Vendor-alt-phone" name="alt_phone" class="form-control" placeholder="Enter Vendor alternative phone...." value="{{ old('alt_phone') }}">
                                    @error('alt_phone')
                                        <div class="text-danger mt-1">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mt-1 mb-1 col-12 col-md-6 col-lg-4 col-xl-4">
                                    <label class="form-label" for="status">Vendor Status</label>
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
    document.getElementById('apply-course-form').addEventListener('submit', function(event) {
        let isValid = true;

        // Select all fields that are required
        const requiredFields = document.querySelectorAll('.required-label + input');

        requiredFields.forEach(function(field) {
            // Clear any existing errors
            field.nextElementSibling?.remove();

            // Check if the field is empty
            if (!field.value.trim()) {
                isValid = false;
                const errorMessage = document.createElement('div');
                errorMessage.className = 'text-danger mt-1';
                errorMessage.textContent = 'This field is required.';
                field.parentElement.appendChild(errorMessage);
            }
        });

        // Prevent form submission if validation fails
        if (!isValid) {
            event.preventDefault();
        }
    });

</script>
@endsection
