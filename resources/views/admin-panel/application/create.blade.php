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
        /* display: flex; */
    }

    .hide_data{
        display:none;
    }
    #error_messsage{
        display:none;
        color:red;
    }

</style>

<div class="content-wrapper">
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="row">
            <div class="col-xl">
                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Apply For New Courses</h5>
                    </div>
                    <div class="card-body">
                        <form id="apply-course-form" method="POST">
                            @csrf
                            <div class="row">
                                <div class="mt-1 mb-1 col-12 col-md-6 col-lg-4 col-xl-4">
                                    <label class="form-label required-label" for="intake">Intake</label>
                                    <select class="form-control select2" name="intake" id="intake">
                                        <option value="">Select Intake</option>
                                        @foreach($intakes as $intake)
                                        <option value="{{ $intake['id'] }}">
                                            {{ $intake['name'] }}
                                        </option>
                                        @endforeach
                                    </select>
                                    @error('intake')
                                    <div class="text-danger mt-1">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="mt-1 mb-1 col-12 col-md-6 col-lg-4 col-xl-4 hide_data" id="course_type_div" >
                                    <label class="form-label required-label" for="course_type">Courses type</label>
                                    <select class="form-control select2" id="course_type" name="course_type">
                                        <option value="">Select Course Type</option>
                                        @foreach($courseTypes as $key => $value)
                                        <option value="{{ $key }}" {{ old('course_type') == $key ? 'selected' : '' }}>{{ $value }}</option>
                                        @endforeach
                                    </select>
                                    @error('course_type')
                                    <div class="text-danger mt-1">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="mt-1 mb-1 col-12 col-md-6 col-lg-4 col-xl-4 hide_data" id="university_div" >
                                    <label class="form-label required-label" for="university">University</label>
                                    <select class="form-control select2" id="university_id" name="university_id">
                                        <option value="">Select University</option>
                                        @foreach($universities as $university)
                                        <option value="{{ $university['id'] }}" {{ old('university_id') == $university['id'] ? 'selected' : '' }}>{{ $university['name'] }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="mt-1 mb-1 col-12 col-md-6 col-lg-4 col-xl-4 hide_data"  id="course_div" >
                                    <label class="form-label required-label" for="courses">Courses</label>

                                    <select class="form-control select2" id="courses" name="courses">
                                        <option value="">Select Course</option>
                                        @foreach($courses as $course)
                                        <option value="{{ $course['id'] }}" {{ old('courses') == $course['id'] ? 'selected' : '' }}>{{ $course['course_name'] }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <p id="error_messsage"></p>
                                <div class="mt-2 mb-1 col-12 col-md-12 col-lg-12 col-xl-12">
                                    <p type="submit"  onclick="GetDetailData()" class="btn btn-primary mt-1">Apply New Course</p>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <div id="detail_page_applicatio_offer">
            <!-- Content will be loaded here -->
        </div>
    </div>
</div>

<!-- Include jQuery -->

<script>

function GetDetailData(){
    var intake = $('#intake').val();
    if (!intake) {
       $('#error_messsage').html("select Any One Filter");
        $('#error_messsage').show();
       return 0 ;
    }else{
        $('#error_messsage').hide();
    }
            $('#detail_page_applicatio_offer').html(`
                    <div class="card w-100">
                        @include('admin-panel.components.loader')
                    </div> 
                `);

            $.ajax({
                url: '{{ route('application.detail-fetch') }}', 
                type: 'POST', 
                data: $('#apply-course-form').serialize(), 
                success: function(response) {
                    $('#detail_page_applicatio_offer').html(response.html);
                },
                error: function(xhr) {
                    {
                        {
                            $('#detail_page_applicatio_offer').html("Error Occurred. Please try again.");
                        }
                    }
                }
            });
        }


    $(document).ready(function() {
        // Setup CSRF token for AJAX requests
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $('#apply-course-form').on('submit', function(e) {
            e.preventDefault(); 
        });


        



        $('#intake').select2().on('change', function() {
        let intake = $(this).val();
        $('#course_type').empty();
        $('#course_type_div').hide();

        $('#university_id').empty();
        $('#university_div').hide();

        $('#courses').empty();
        $('#course_div').hide();
        
        if (intake) {
            Swal.fire({
                title: 'Loading...',
                allowOutsideClick: false,
                onBeforeOpen: () => {
                    Swal.showLoading();
                }
            });

            $.ajax({
                url: '{{ route('application.getCourseType') }}',
                type: 'POST',
                data: $('#apply-course-form').serialize(), 
                success: function(response) {
                    Swal.close(); // Close the loader

                    let courseTypeSelect = $('#course_type');
                    courseTypeSelect.empty();
                    courseTypeSelect.append('<option value="">Select Course Type</option>');

                    if (response.courses.length > 0) {
                        response.courses.forEach(function(course) {
                            courseTypeSelect.append('<option value="' + course.course_type + '">' + course.course_type.charAt(0).toUpperCase() + course.course_type.slice(1) + '</option>');
                        });

                        $('#course_type_div').css('display', 'grid');
                    } else {
                        $('#course_type_div').css('display', 'none');
                    }
                },
                error: function(xhr) {
                    Swal.close(); 
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'An error occurred while fetching data. Please try again.'
                    });
                }
            });
        } else {
            $('#course_type_div').css('display', 'none'); 
        }
    });

    $('#course_type').select2().on('change', function() {
        let course_type = $(this).val();
        

        $('#university_id').empty();
        $('#university_div').hide();

        $('#courses').empty();
        $('#course_div').hide();
        console.log("ewkjgewk",course_type);
        if (course_type) {
            // Show SweetAlert loader
            Swal.fire({
                title: 'Loading...',
                allowOutsideClick: false,
                onBeforeOpen: () => {
                    Swal.showLoading();
                }
            });

            $.ajax({
                url: '{{ route('application.getUniversityList') }}',
                type: 'POST',
                data: $('#apply-course-form').serialize(), 
                success: function(response) {
                    Swal.close(); // Close the loader

                    let courseTypeSelect = $('#university_id');
                    courseTypeSelect.empty();
                    courseTypeSelect.append('<option value="">Select Courses</option>');

                    if (response.university.length > 0) {
                        response.university.forEach(function(university) {
                            courseTypeSelect.append('<option value="' + university.id + '">' + university.name.charAt(0).toUpperCase() + university.name.slice(1) + '</option>');
                        });
                        $('#university_div').css('display', 'grid');
                    } else {
                        // Hide the course type dropdown if no courses are available
                        $('#university_div').css('display', 'none');
                    }
                },
                error: function(xhr) {
                    Swal.close(); // Close the loader
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'An error occurred while fetching data. Please try again.'
                    });
                }
            });
        } else {
            $('#university_div').css('display', 'none'); // Hide additional fields if no intake is selected
        }
    });

    $('#university_id').select2().on('change', function() {
        let university_id = $(this).val();
       
        $('#courses').empty();
        $('#course_div').hide();
        console.log("ewkjgewk",university_id);
        if (university_id) {
            // Show SweetAlert loader
            Swal.fire({
                title: 'Loading...',
                allowOutsideClick: false,
                onBeforeOpen: () => {
                    Swal.showLoading();
                }
            });

            $.ajax({
                url: '{{ route('application.getCourse') }}',
                type: 'POST',
               data: $('#apply-course-form').serialize(), 
                success: function(response) {
                    Swal.close(); 

                    let courseTypeSelect = $('#courses');
                    courseTypeSelect.empty();
                    courseTypeSelect.append('<option value="">Select university</option>');

                    if (response.courses.length > 0) {
                        response.courses.forEach(function(courses) {
                            courseTypeSelect.append('<option value="' + courses.id + '">' + courses.name.charAt(0).toUpperCase() + courses.name.slice(1) + '</option>');
                        });
                        $('#course_div').css('display', 'grid');
                    } else {
                        $('#course_div').css('display', 'none');
                    }
                },
                error: function(xhr) {
                    Swal.close(); 
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'An error occurred while fetching data. Please try again.'
                    });
                }
            });
        } else {
            $('#course_div').css('display', 'none'); 
        }
    });


    });


    

</script>
@endsection
