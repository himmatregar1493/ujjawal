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
                        <h5 class="mb-0">Product Create</h5>
                        <a href="{{Route('product.index')}}"><button id="create-Product-btn" class="mb-0 btn btn-primary">Back</button></a>
                    </div>
                    <div class="card-body pb-2 p-2 pt-3">
                        <form id="apply-course-form" method="POST" action="{{ route('product.store') }}">
                            @csrf
                            <div class="row">
                                <!-- Existing fields -->
                                <!-- Product Name -->
                                <div class="mt-1 mb-1 col-12 col-md-6 col-lg-4 col-xl-4">
                                    <label class="form-label required-label" for="Product-name">Product Name</label>
                                    <input type="text" id="Product-name" name="name" class="form-control" placeholder="Enter Product name...." value="{{ old('name') }}">
                                    @error('name')
                                        <div class="text-danger mt-1">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- New GST fields -->
                               <div class="mt-1 mb-1 col-12 col-md-6 col-lg-4 col-xl-4">
                                    <label class="form-label" for="cgst">CGST (%)</label>
                                    <input type="text" id="cgst" name="cgst" class="form-control" placeholder="Enter CGST..." value="{{ old('cgst') }}" oninput="validatePercentage(this)" />
                                    @error('cgst')
                                        <div class="text-danger mt-1">{{ $message }}</div>
                                    @enderror
                                </div>

                               <div class="mt-1 mb-1 col-12 col-md-6 col-lg-4 col-xl-4">
                                    <label class="form-label" for="sgst">SGST (%)</label>
                                    <input type="text" id="sgst" name="sgst" class="form-control" placeholder="Enter SGST..." value="{{ old('sgst') }}" oninput="validatePercentage(this)" />
                                    @error('sgst')
                                        <div class="text-danger mt-1">{{ $message }}</div>
                                    @enderror
                                </div>

                               <div class="mt-1 mb-1 col-12 col-md-6 col-lg-4 col-xl-4">
                                    <label class="form-label" for="igst">IGST (%)</label>
                                    <input type="text" id="igst" name="igst" class="form-control" placeholder="Enter IGST..." value="{{ old('igst') }}" oninput="validatePercentage(this)" />
                                    @error('igst')
                                        <div class="text-danger mt-1">{{ $message }}</div>
                                    @enderror
                                </div>

                               <div class="mt-1 mb-1 col-12 col-md-6 col-lg-4 col-xl-4">
                                    <label class="form-label" for="utgst">UTGST (%)</label>
                                    <input type="text" id="utgst" name="utgst" class="form-control" placeholder="Enter UTGST..." value="{{ old('utgst') }}" oninput="validatePercentage(this)" />
                                    @error('utgst')
                                        <div class="text-danger mt-1">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Price field -->
                                <div class="mt-1 mb-1 col-12 col-md-6 col-lg-4 col-xl-4">
                                    <label class="form-label" for="price">Per Unit Price</label>
                                    <input type="text" id="price" name="price" class="form-control" placeholder="Enter Price..." value="{{ old('price') }}" pattern="^\d+(\.\d{1,2})?$" oninput="validatePriceInput(this)">
                                    @error('price')
                                        <div class="text-danger mt-1">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Product Unit field -->
                               <div class="mt-1 mb-1 col-12 col-md-6 col-lg-4 col-xl-4">
                                    <label class="form-label" for="unit">Product Unit</label>
                                    <select id="unit" name="unit" class="form-control">
                                        <option value="">Select Unit</option>
                                        @foreach(config('constants.product_units') as $key => $value)
                                            <option value="{{ $key }}" {{ old('unit') == $key ? 'selected' : '' }}>{{ $value }}</option>
                                        @endforeach
                                    </select>
                                    @error('unit')
                                        <div class="text-danger mt-1">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Product Status -->
                                <div class="mt-1 mb-1 col-12 col-md-6 col-lg-4 col-xl-4">
                                    <label class="form-label" for="status">Product Status</label>
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
<script>
    function validatePercentage(input) {
        let value = input.value;
        if (!/^\d*\.?\d*$/.test(value)) {
            // If the value is not a valid number, clear the input
            input.value = '';
            return;
        }

        let numericValue = parseFloat(value);
        if (numericValue < 0 || numericValue > 100) {
            // If the value is out of range, clear the input
            input.value = '';
        }
    }
    </script>
    <script>
        function validatePriceInput(input) {
            const value = input.value;
            const regex = /^\d+(\.\d{1,2})?$/;

            if (!regex.test(value)) {
                input.setCustomValidity("Please enter a valid price (e.g., 100.3, 300.54).");
            } else {
                input.setCustomValidity(""); // Clear the validation message
            }
        }
    </script>

@endsection
