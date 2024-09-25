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

</style>
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
<div class="content-wrapper">
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="row">
            <div class="col-xl">
                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center pb-2 p-2 pt-3">
                        <h5 class="mb-0">Application Id ({{@$application['id']}})</h5>
                        <a href="{{ route('role.index') }}">
                            <button id="create-role-btn" class="mb-0 btn btn-primary">Back</button>
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

                        <form id="apply-course-form" method="POST" action="{{ route('role.update', 1) }}">
                            @csrf
                            <div class="row">




                                <div class="col-12 col-md-12 col-lg-12 col-xl-12 mt mb-2">
                                    <ul class="nav nav-tabs" id="myTab" role="tablist">
                                        <li class="nav-item mt-2" role="presentation"  onclick="studentDetail()">
                                            <a class="btn btn-secondary active mt-1 w-auto ml-2" data-bs-toggle="tab" href="#student-info" role="tab" aria-controls="student-info" aria-selected="true">Student Details</a>
                                        </li>

                                        <li class="nav-item mt-2" role="presentation"  onclick="CourseDetail()">
                                            <a class="btn btn-secondary mt-1 w-auto ml-2" data-bs-toggle="tab" href="#course-details" role="tab" aria-controls="course-details" aria-selected="false">Course Details</a>
                                        </li>

                                        <li class="nav-item mt-2" role="presentation" onclick="GetUploadDownload()">
                                            <a class="btn btn-secondary mt-1 w-auto ml-2" data-bs-toggle="tab" href="#student-document" role="tab" aria-controls="student-document" aria-selected="false">Upload / Download</a>
                                        </li>

                                        <li class="nav-item mt-2" role="presentation" onclick="GetApplicationHistory()">
                                            <a class="btn btn-secondary mt-1 w-auto ml-2" data-bs-toggle="tab" href="#application-history" role="tab" aria-controls="application-history" aria-selected="false">Application History</a>
                                        </li>

                                        <li class="nav-item mt-2" role="presentation" onclick="GetAddComments()">
                                            <a class="btn btn-secondary mt-1 w-auto ml-2" data-bs-toggle="tab" href="#add-comments" role="tab" aria-controls="add-comments" aria-selected="false">Add Comments</a>
                                        </li>

                                        {{-- <li class="nav-item mt-2" role="presentation" onclick="GetUniversityCommunication()">
                                            <a class="btn btn-secondary mt-1 w-auto ml-2" data-bs-toggle="tab" href="#university-communication" role="tab" aria-controls="university-communication" aria-selected="false">University Communication</a>
                                        </li> --}}

                                        {{-- <li class="nav-item mt-2" role="presentation" onclick="GetApplyNewCourse()">
                                            <a class="btn btn-secondary mt-1 w-auto ml-2" data-bs-toggle="tab" href="#apply-new-course" role="tab" aria-controls="apply-new-course" aria-selected="false">Apply For New Course</a>
                                        </li> --}}

                                        {{-- <li class="nav-item mt-2" role="presentation" onclick="GetCallRecordings()">
                                            <a class="btn btn-secondary mt-1 w-auto ml-2" data-bs-toggle="tab" href="#call-recordings" role="tab" aria-controls="call-recordings" aria-selected="false">Call Recordings</a>
                                        </li> --}}

                                        <li class="nav-item mt-2" role="presentation" onclick="URM_Detail()">
                                            <a class="btn btn-secondary mt-1 w-auto ml-2" data-bs-toggle="tab" href="#URM_Detail" role="tab" aria-controls="URM_Detail" aria-selected="false">URM Detail</a>
                                        </li>
                                        @if(UserCan('application.assign_view'))
                                         <li class="nav-item mt-2" role="presentation" onclick="assign_application()">
                                            <a class="btn btn-secondary mt-1 w-auto ml-2" data-bs-toggle="tab" href="#assign_application" role="tab" aria-controls="assign_application" aria-selected="false">Assign Application</a>
                                        </li>
                                        @endif
                                    </ul>
                                    <hr>
                                    <div class="tab-content" id="myTabContent">
                                    
                                        <div class="tab-pane fade show active" id="student-info" role="tabpanel" aria-labelledby="student-info-tab">
                                            <div class="d-flex w-100">
                                                @include('admin-panel.components.loader')
                                            </div> 
                                        </div>
                                        <div class="tab-pane fade" id="course-details" role="tabpanel" aria-labelledby="course-details-tab">
                                            ewewqewqewq
                                        </div>
                                        <div class="tab-pane fade" id="student-document" role="tabpanel" aria-labelledby="student-document-tab">
                                            ewqewqewqe
                                        </div>
                                        <div class="tab-pane fade" id="application-history" role="tabpanel" aria-labelledby="application-history-tab">
                                            <!-- Content for Application History -->
                                        </div>
                                        <div class="tab-pane fade" id="add-comments" role="tabpanel" aria-labelledby="add-comments-tab">
                                            <!-- Content for Add Comments -->
                                        </div>
                                        <div class="tab-pane fade" id="university-communication" role="tabpanel" aria-labelledby="university-communication-tab">
                                            <!-- Content for University Communication -->
                                        </div>
                                        <div class="tab-pane fade" id="apply-new-course" role="tabpanel" aria-labelledby="apply-new-course-tab">
                                            <!-- Content for Apply For New Course -->
                                        </div>
                                        <div class="tab-pane fade" id="call-recordings" role="tabpanel" aria-labelledby="call-recordings-tab">
                                            <!-- Content for Call Recordings -->
                                        </div>
                                        <div class="tab-pane fade" id="URM_Detail" role="tabpanel" aria-labelledby="URM_Detail-tab">
                                          
                                        </div>  

                                        <div class="tab-pane fade" id="assign_application" role="tabpanel" aria-labelledby="assign_application-tab">
                                          
                                        </div>  
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
        studentDetail();
    });

    function clearTabContent() {
        $('.tab-pane').each(function() {
            $(this).empty(); // Clear the content
        });
    }

    $('#myTab').on('click', 'li', function() {
        clearTabContent();
    });

     $('#apply-course-form').on('submit', function(e) {
            e.preventDefault(); 
        });
    function studentDetail() {
        clearTabContent();
       $('#student-info').html(`
                <div class="d-flex w-100">
                    @include('admin-panel.components.loader')
                </div> 
            `);

        $.ajax({
            url: '{{ route('application-history.student-info') }}',
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

    function CourseDetail() {
        $('#course-details').html(`
                <div class="d-flex w-100">
                    @include('admin-panel.components.loader')
                </div> 
            `);

        $.ajax({
            url: '{{ route('application-history.course_detail') }}',
             type: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
            },
             data: {
                application_id: {{$application['id']}}
            },
             success: function(response) {
                $('#course-details').html(response);
            },
             error: function(xhr) {
                $('#course-details').html("Error Occurred. Please try again.");
            }
        });
    }
    
    function GetUploadDownload() {
        $('#student-document').html(`
                <div class=" d-flex w-100">
                     @include('admin-panel.components.loader')
                </div> 
            `);
        $.ajax({
            url: '{{ route('application-history.student-document') }}',
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

    function GetApplicationHistory() {
       $('#application-history').html(`
                <div class=" d-flex w-100">
                     @include('admin-panel.components.loader')
                </div> 
            `);
        $.ajax({
            url: '{{ route('application-history.application-history') }}',
            type: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
             data: {
                application_id: {{$application['id']}}
            },
            success: function(response) {
                $('#application-history').html(response);
            }, 
            error: function(xhr) {
                $('#application-history').html("Error Occurred. Please try again.");
            }
        });
    }

    function GetAddComments() {
        $('#add-comments').html(`
                <div class=" d-flex w-100">
                     @include('admin-panel.components.loader')
                </div> 
            `);
        $.ajax({
            url: '{{ route('application-history.comments') }}',
            type: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
             data: {
                application_id: {{$application['id']}}
            },
            success: function(response) {
                $('#add-comments').html(response);
            }, 
            error: function(xhr) {
                $('#add-comments').html("Error Occurred. Please try again.");
            }
        });
    }

    function assign_application(){
        $('#assign_application').html(`
                <div class=" d-flex w-100">
                     @include('admin-panel.components.loader')
                </div> 
            `);
        $.ajax({
            url: '{{ route('application-history.assign_application') }}',
            type: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
             data: {
                application_id: {{$application['id']}}
            },
            success: function(response) {
                $('#assign_application').html(response);
            }, 
            error: function(xhr) {
                $('#assign_application').html("Error Occurred. Please try again.");
            }
        });

    }

    function GetUniversityCommunication() {
        console.log('University Communication tab clicked');
        // Your logic here
    }

    function GetApplyNewCourse() {
        console.log('Apply For New Course tab clicked');
        // Your logic here
    }

    function GetCallRecordings() {
        console.log('Call Recordings tab clicked');
        // Your logic here
    }

    function URM_Detail() {
         $('#URM_Detail').html(`
                <div class=" d-flex w-100">
                     @include('admin-panel.components.loader')
                </div> 
            `);
        $.ajax({
            url: '{{ route('application-history.urm_detail') }}',
            type: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
             data: {
                application_id: {{$application['id']}}
            },
            success: function(response) {
                $('#URM_Detail').html(response);
            }, 
            error: function(xhr) {
                $('#URM_Detail').html("Error Occurred. Please try again.");
            }
        });
    }

</script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const roleCheckboxes = document.querySelectorAll('.role-checkbox');
        const permissionCheckboxes = document.querySelectorAll('.permission-checkbox');

        // Function to update the state of parent checkboxes
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

        // Event listener for role checkboxes to update all related permission checkboxes
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

        // Event listener for permission checkboxes to update the related role checkbox
        permissionCheckboxes.forEach(permissionCheckbox => {
            permissionCheckbox.addEventListener('change', updateParentCheckbox);
        });

        // Initial update to set the correct state of parent checkboxes
        updateParentCheckbox();
    });

</script>
@endsection
