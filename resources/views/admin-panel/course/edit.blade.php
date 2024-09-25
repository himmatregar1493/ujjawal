@extends('layouts.admin.app')

@section('content')
<style>
    .custom-img {
        width: 100%;
        max-width:  100px;
        height: auto;
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
                        <h5 class="mb-0">Course Edit</h5>
                        <a href="{{ route('course.index') }}">
                            <button id="create-course-btn" class="mb-0 btn btn-primary">Back</button>
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

                        <form id="apply-course-form" method="POST" action="{{ route('course.update', $course->id) }}" enctype="multipart/form-data" >
                            @csrf
                            @method('PUT')
                            <div class="row">

                                <!-- Course Name -->
                                <div class="mt-1 mb-1 col-12 col-md-6 col-lg-4 col-xl-4">
                                    <label class="form-label required-label" for="course_name">Course Name</label>
                                    <input type="text" name="course_name" class="form-control" placeholder="Enter course name..." value="{{ old('course_name', $course->course_name) }}">
                                    @error('course_name')
                                    <div class="text-danger mt-1">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Course Type -->
                                <div class="mt-1 mb-1 col-12 col-md-6 col-lg-4 col-xl-4">
                                    <label class="form-label" for="course_type">Course Type</label>
                                    <select class="form-control select2" id="course_type" name="course_type">
                                        <option value="">Select Course Type</option>
                                        @foreach($courseTypes as $key => $value)
                                        <option value="{{ $key }}" {{ old('course_type', $course->course_type) == $key ? 'selected' : '' }}>{{ $value }}</option>
                                        @endforeach
                                    </select>
                                    @error('course_type')
                                    <div class="text-danger mt-1">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mt-1 mb-1 col-12 col-md-6 col-lg-4 col-xl-4">
                                    <label class="form-label required-label" for="location">location</label>
                                    <input type="text" name="location" class="form-control" placeholder="Enter course name..." value="{{ old('location',$course->location) }}">
                                    @error('location')
                                    <div class="text-danger mt-1">{{ $message }}</div>
                                    @enderror
                                </div>


                                <!-- Select Country -->
                                <div class="mt-1 mb-1 col-12 col-md-6 col-lg-4 col-xl-4">
                                    <label class="form-label required-label" for="country">Country</label>
                                    <select class="form-control select2" id="country" name="country" onchange="CountryGet()">
                                        <option value="">Select Country</option>
                                        @foreach($countries as $country)
                                        <option value="{{ $country['id'] }}" {{ old('country', $course->country) == $country['id'] ? 'selected' : '' }}>{{ $country['name'] }}</option>
                                        @endforeach
                                    </select>
                                    @error('country')
                                    <div class="text-danger mt-1">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Select State -->
                                <div class="mt-1 mb-1 col-12 col-md-6 col-lg-4 col-xl-4">
                                    <label class="form-label required-label" for="state">State</label>
                                    <select class="form-control select2" id="state" name="state">
                                        <option value="">Select State</option>
                                    </select>
                                    @error('state')
                                    <div class="text-danger mt-1">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Select University -->
                                <div class="mt-1 mb-1 col-12 col-md-6 col-lg-4 col-xl-4">
                                    <label class="form-label required-label" for="university_id">University</label>
                                    <select class="form-control select2" id="university_id" name="university_id">
                                        <option value="">Select University</option>
                                        @foreach($universities as $university)
                                        <option value="{{ $university['id'] }}" {{ old('university_id', $course->university_id) == $university['id'] ? 'selected' : '' }}>{{ $university['name'] }}</option>
                                        @endforeach
                                    </select>
                                    @error('university_id')
                                    <div class="text-danger mt-1">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Select Intake -->
                                <div class="mt-1 mb-1 col-12 col-md-6 col-lg-4 col-xl-4">
                                    <label class="form-label" for="intake">Select Intake</label>
                                    @php
                                    // Assuming $course->intake contains a comma-separated string of intake IDs
                                    $intakesList = explode(',', $course->intake);
                                  
                                    @endphp

                                    <select class="form-control select2" name="intake[]" multiple>
                                        <option value="">Select Intake</option>
                                        @foreach($intakes as $intake)
                                        <option value="{{ $intake['id'] }}" {{ in_array($intake['id'], old('intake', $intakesList)) ? 'selected' : '' }}>
                                            {{ $intake['name'] }}
                                        </option>
                                        @endforeach
                                    </select>
                                    @error('intake')
                                    <div class="text-danger mt-1">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Course Status -->
                                <div class="mt-1 mb-1 col-12 col-md-6 col-lg-4 col-xl-4">
                                    <label class="form-label required-label" for="is_active">Course Status</label>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', $course->is_active) ? 'checked' : '' }}>
                                    </div>
                                    @error('is_active')
                                    <div class="text-danger mt-1">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Application Fee -->
                                <div class="mt-1 mb-1 col-12 col-md-6 col-lg-4 col-xl-4">
                                    <label class="form-label" for="application_fee">Application Fee</label>
                                    <input type="number" step="0.01" name="application_fee" class="form-control" placeholder="Enter application fee..." value="{{ old('application_fee', $course->application_fee) }}">
                                    @error('application_fee')
                                    <div class="text-danger mt-1">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Tuition Fees -->
                                <div class="mt-1 mb-1 col-12 col-md-6 col-lg-4 col-xl-4">
                                    <label class="form-label" for="tuition_fees_inr">Tuition Fees (INR)</label>
                                    <input type="number" step="0.01" name="tuition_fees_inr" class="form-control" placeholder="Enter tuition fees..." value="{{ old('tuition_fees_inr', $course->tuition_fees_inr) }}">
                                    @error('tuition_fees_inr')
                                    <div class="text-danger mt-1">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Duration -->
                                <div class="mt-1 mb-1 col-12 col-md-6 col-lg-4 col-xl-4">
                                    <label class="form-label" for="duration">Duration</label>
                                    <input type="text" name="duration" class="form-control" placeholder="Enter course duration..." value="{{ old('duration', $course->duration) }}">
                                    @error('duration')
                                    <div class="text-danger mt-1">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Web URL -->
                                <div class="mt-1 mb-1 col-12 col-md-6 col-lg-4 col-xl-4">
                                    <label class="form-label" for="web_url">Web URL</label>
                                    <input type="url" name="web_url" class="form-control" placeholder="Enter web URL..." value="{{ old('web_url', $course->web_url) }}">
                                    @error('web_url')
                                    <div class="text-danger mt-1">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Campus -->
                                <div class="mt-1 mb-1 col-12 col-md-6 col-lg-4 col-xl-4">
                                    <label class="form-label" for="campus">Campus</label>
                                    <input type="text" name="campus" class="form-control" placeholder="Enter campus details..." value="{{ old('campus', $course->campus) }}">
                                    @error('campus')
                                    <div class="text-danger mt-1">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Course Description -->
                                <div class="mt-1 mb-1 col-12 col-md-6 col-lg-6 l-xl-6">
                                    <label class="form-label" for="description">Course Description</label>
                                    <textarea class="form-control" name="description" rows="4">{{ old('description', $course->description) }}</textarea>
                                    @error('description')
                                    <div class="text-danger mt-1">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mt-1 mb-1 col-12 col-md-6 col-lg-6 col-xl-6">
                                    <label class="form-label" for="cover_image">Cover Image</label>
                                    <input type="file" class="form-control" id="cover_image" name="cover_image" accept="image/*">
                                    <div class="mt-2">
                                        <img id="cover_image_preview" class="custom-img" alt="Cover Image Preview" style="display: none;">
                                    </div>
                                    @if($course->cover_image)
                                    <img src="{{ asset('admin_assets\images\course_image') }}\{{$course->cover_image}}" class="custom-img mt-2" alt="Cover Image">
                                    @endif
                                    @error('cover_image')
                                    <div class="text-danger mt-1">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Action Buttons -->
                                <div class="col-12">
                                    <div class="d-flex justify-content-end">
                                        <button type="submit" class="btn btn-primary">Update</button>
                                        <button type="reset" class="btn btn-secondary ms-2">Reset</button>
                                    </div>
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
    $(document).ready(function() {
        // Initialize select2 for dropdowns
        $('.select2').select2();
 CountryGet();
        // Load states based on selected country
        function CountryGet() {
    var alreadySelected = '{{ $course->state }}'; // Ensure this is a string to match option values
    var selectedCountry = $('#country').val();

    if (selectedCountry) {
        Swal.fire({
            title: 'Loading...',
            allowOutsideClick: false,
            onBeforeOpen: () => {
                Swal.showLoading();
            }
        });

        $.ajax({
            url: '{{ route('state.get') }}',
            type: 'POST',
            data: {
                country: selectedCountry,
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                Swal.close();
                $('#state').empty();
                if (response.length > 0) {
                    $('#state').append('<option value="">Select State</option>');
                    response.forEach(function(state) {
                        // Add the selected attribute if the state ID matches alreadySelected
                        $('#state').append('<option value="' + state.id + '"' + (alreadySelected == state.id ? ' selected' : '') + '>' + state.name + '</option>');
                    });
                } else {
                    $('#state').append('<option value="">No states available</option>');
                }
                $('#state').select2(); // Reinitialize select2
            },
            error: function(xhr, status, error) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'An error occurred while fetching data.'
                });
            }
        });
    }
}

$('#country').select2().on('change', function() {
    CountryGet();
});

        // Trigger CountryGet() on page load if a country is already selected
       

        $('#cover_image').on('change', function() {
            var file = this.files[0];
            if (file) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    $('#cover_image_preview').attr('src', e.target.result).show();
                };
                reader.readAsDataURL(file);
            } else {
                $('#cover_image_preview').hide();
            }
        });
    });

</script>
@endsection
