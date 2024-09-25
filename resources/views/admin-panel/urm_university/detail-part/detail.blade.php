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

</style>
<style>
    .nav-item .nav-link.active {
        color: white !important;
        /* Text color for active tab */
        background-color: rgb(239, 239, 253) !important;
        /* Background color for active tab */
    }

    .nav-item {
        margin-left: 10px;
        margin-right: 10px;
    }

    .btn-secondary:active,
    .btn-secondary.active,
    .btn-secondary.show.dropdown-toggle,
    .show>.btn-secondary.dropdown-toggle {
        color: #fff !important;
        background-color: #696cff !important;
        border-color: #696cff !important;
    }

    .nav-tabs {
        border: 0px !important;
    }

</style>
{{-- @if(isset($application))
        <script>
            Swal.fire({
                title: 'Your Temporary Application ID is {{ $application['id'] }}',
allowOutsideClick: false,
});
</script>
@endif --}}
<div class="content-wrapper">
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="row">
            <div class="col-xl">
                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Selected Courses</h5>
                        <h5>Your Application Id {{ $application['id'] }}</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="mt-1 mb-1 col-12 col-md-6 col-lg-4 col-xl-4">
                                <img src="{{ asset('admin_assets\images\course_image') }}\{{@$course['cover_image']}}" class="img-fluid rounded" alt="Responsive image">
                            </div>
                            <div class="mt-1 mb-1 col-12 col-md-6 col-lg-8 col-xl-8">
                                <h3>{{@$course['university_name']}}</h3>
                                <div class="row col-12 col-md-12 col-lg-12 col-xl-12 mt-2 mb-2">
                                    <div class="col-5 col-sm-5 col-md-5 col-lg-5">
                                        <b>Institute Name</b>
                                    </div>
                                    <div class="col-2 col-sm-2 col-md-2 col-lg-2">:</div>
                                    <div class="col-5 col-sm-5 col-md-5 col-lg-5">{{@$course['institute_name']}}</div>
                                </div>
                                <div class="row col-12 col-md-12 col-lg-12 col-xl-12 mt-2 mb-2">
                                    <div class="col-5 col-sm-5 col-md-5 col-lg-5">
                                        <b>Course Name</b>
                                    </div>
                                    <div class="col-2 col-sm-2 col-md-2 col-lg-2">:</div>
                                    <div class="col-5 col-sm-5 col-md-5 col-lg-5">{{@$course['course_name']}}</div>
                                </div>

                                <div class="row col-12 col-md-12 col-lg-12 col-xl-12 mt-2 mb-2">
                                    <div class="col-5 col-sm-5 col-md-5 col-lg-5">
                                        <b>Course Intake</b>
                                    </div>
                                    <div class="col-2 col-sm-2 col-md-2 col-lg-2">:</div>
                                    <div class="col-5 col-sm-5 col-md-5 col-lg-5">{{@$course['intake']}}</div>
                                </div>
                                <div class="row col-12 col-md-12 col-lg-12 col-xl-12 mt-2 mb-2">
                                    <div class="col-5 col-sm-5 col-md-5 col-lg-5">
                                        <b>Campus</b>
                                    </div>
                                    <div class="col-2 col-sm-2 col-md-2 col-lg-2">:</div>
                                    <div class="col-5 col-sm-5 col-md-5 col-lg-5">{{@$course['campus']}}</div>
                                </div>
                                <div class="row col-12 col-md-12 col-lg-12 col-xl-12 mt-2 mb-2">
                                    <div class="col-5 col-sm-5 col-md-5 col-lg-5">
                                        <b>Tution Fees</b>
                                    </div>
                                    <div class="col-2 col-sm-2 col-md-2 col-lg-2">:</div>
                                    <div class="col-5 col-sm-5 col-md-5 col-lg-5">{{@$course['tuition_fees_inr']}} </div>
                                </div>
                                <div class="row col-12 col-md-12 col-lg-12 col-xl-12 mt-2 mb-2">
                                    <div class="col-5 col-sm-5 col-md-5 col-lg-5">
                                        <b>Duration</b>
                                    </div>
                                    <div class="col-2 col-sm-2 col-md-2 col-lg-2">:</div>
                                    <div class="col-5 col-sm-5 col-md-5 col-lg-5">{{@$course['duration']}}</div>
                                </div>
                                <div class="row col-12 col-md-12 col-lg-12 col-xl-12 mt-2 mb-2">
                                    <div class="col-5 col-sm-5 col-md-5 col-lg-5">
                                        <b>Course Type</b>
                                    </div>
                                    <div class="col-2 col-sm-2 col-md-2 col-lg-2">:</div>
                                    <div class="col-5 col-sm-5 col-md-5 col-lg-5">{{ ucfirst(@$course['course_type']) }}</div>
                                </div>
                                <div class="row col-12 col-md-12 col-lg-12 col-xl-12 mt-2 mb-2">
                                    <div class="col-5 col-sm-5 col-md-5 col-lg-5">
                                        <b>Location</b>
                                    </div>
                                    <div class="col-2 col-sm-2 col-md-2 col-lg-2">:</div>
                                    <div class="col-5 col-sm-5 col-md-5 col-lg-5">{{@$course['location']}}</div>
                                </div>

                                {{-- <div class="row col-12 col-md-12 col-lg-12 col-xl-12 mt-2 mb-2">
                                    <div class="col-5 col-sm-12 col-md-12 col-lg-12">
                                        <p class="btn btn-primary">Submit Application </p>
                                    </div>
                                    
                                </div> --}}
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Process Application</h5>
                    </div>
                    
                    <div class="card-body">
                        <div class="row">
                            <div class="col-12 col-md-12 col-lg-12 col-xl-12 mt mb-2">
                                <ul class="nav nav-tabs" id="myTab" role="tablist">
                                    <li class="nav-item mt-2" role="presentation">
                                        <a class="btn btn-secondary active mt-1 w-auto ml-2" id="student-info-tab" data-bs-toggle="tab" href="#student-info" role="tab" aria-controls="student-info" aria-selected="true">Students Information</a>
                                    </li>
                                    <li class="nav-item  mt-2" role="presentation" onclick="GetStudentDocument()">
                                        <a class="btn btn-secondary mt-1 w-auto ml-2" id="student-document-tab" data-bs-toggle="tab" href="#student-document" role="tab" aria-controls="student-document" aria-selected="false">Student Document</a>
                                    </li>
                                    <li class="nav-item mt-2 " role="presentation" onclick="GetSubmitButton()">
                                        <a class="btn btn-secondary mt-1 w-auto ml-2" id="tokyo-tab" data-bs-toggle="tab" href="#application_submit" role="tab" aria-controls="application_submit" aria-selected="false">Quick View & Submit Application</a>
                                    </li>
                                </ul>
                                <div class="tab-content" id="myTabContent">
                                    <div class="tab-pane fade show active" id="student-info" role="tabpanel" aria-labelledby="student-info-tab">

                                    </div>
                                    <div class="tab-pane fade" id="student-document" role="tabpanel" aria-labelledby="student-document-tab">
                                       
                                    </div>
                                    <div class="tab-pane fade" id="application_submit" role="tabpanel" aria-labelledby="tokyo-tab">
                                        <div class="row">
                                            {{-- <div class="col-5 col-sm-12 col-md-12 col-lg-12">
                                                <p class="btn btn-primary" onclick="DownloadApplicationInfoPreview()" id="previewButtonApplicationDownlaod"> Preview Pdf Download</p>
                                            </div> --}}
                                            <div class="col-5 col-sm-12 col-md-12 col-lg-12">
                                                <p class="btn btn-primary"  >Submit Application</p>
                                            </div>

                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>

                
            </div>
        </div>
        <div id="detail-page">

        </div>
    </div>
</div>

<!-- Include jQuery -->

<script>
    function GetStudentInfo() {
        $('#student-info').html(`
                <div class="d-flex w-100">
                    @include('admin-panel.components.loader')
                </div> 
            `);

        $.ajax({
            url: '{{ route('application.student-info') }}',
             type: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
            },
             data: {
                application_id: {{$application['id']}}
            },
             success: function(response) {
                $('#student-info').html(response);
            },
             error: function(xhr) {
                $('#student-info').html("Error Occurred. Please try again.");
            }
        });
    }

    function GetStudentDocument() {
        $('#student-document').html(`
                <div class=" d-flex w-100">
                     @include('admin-panel.components.loader')
                </div> 
            `);
        $.ajax({
            url: '{{ route('application.student-document') }}',
            type: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
             data: {
                application_id: {{$application['id']}}
            },
            success: function(response) {
                $('#student-document').html(response);
            }, 
            error: function(xhr) {
                $('#student-document').html("Error Occurred. Please try again.");
            }
        });
    }

    function GetSubmitButton(){
        $('#application_submit').html(`
               @include('admin-panel.components.loader')  Please wait while we process your application. 
            `);
            Swal.fire({
                title: 'Processing...',
                text: 'Please wait while we process your application.',
                icon: 'info',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading(); // Show the loader
                }
            });
        $.ajax({
            url: '{{ route('application.application_submit') }}',
            type: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
             data: {
                application_id: {{$application['id']}}
            },
            success: function(response) {
                console.log(response.message);
                if (response.status === "failed") {
                    let errorMessages = '';

                    if (response.errors && Object.keys(response.errors).length > 0) {
                        // There are errors
                        errorMessages = '<ul>';
                        for (const [key, value] of Object.entries(response.errors)) {
                            errorMessages += `<li>${value}</li>`;
                        }
                        errorMessages += '</ul>';
                    } else {
                        // No specific errors, use the generic message
                        errorMessages = response.message;
                    }

                    $('#application_submit').html(`<div class="alert alert-danger">${errorMessages}</div>`);
                }
                if(response.status == "success") {
                    Swal.fire({
                        title: 'Success!',
                        text: response.message,
                        icon: 'success',
                        timer: 1000, // 1 second timer
                        timerProgressBar: true,
                        showConfirmButton: false, // Hide the confirm button
                        willClose: () => {
                            // Redirect after the timer expires
                            window.location.href = "{{ route('application-history.index') }}";
                        }
                    });
                }else{
                     Swal.close();
                }
                
            }, 
            error: function(xhr) {
                $('#application_submit').html("Error Occurred. Please try again.");
                Swal.close();
                Swal.fire({
                    title: 'Error!',
                    text: 'An error occurred while processing your request.',
                    icon: 'error'
                });
            }
        });
    }

    $(document).ready(function() {
        GetStudentInfo();
    });

    function DownloadApplicationInfoPreview() {
        $('#previewButtonApplicationDownlaod').html(`
                    <style>
                    .loader-image-design{
                        width : 30px;
                    }
                    #previewButtonApplicationDownlaod{
                        padding:0px;
                    }
                    </style>
                    <pre>           </pre>
                     @include('admin-panel.components.loader')
                     <pre>           </pre>
                
            `); {
            
        }
    }

</script>
@endsection
