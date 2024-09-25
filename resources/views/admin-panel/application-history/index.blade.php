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

        .table-responsive {
            margin-top: 20px;
            padding: 14px !important;
        }

        .table th,
        .table td {
            vertical-align: middle;
        }
    </style>

    <div class="content-wrapper">
        <div class="container-xxl flex-grow-1 container-p-y">
            <div class="row">
                <div class="col-xl">
                    <div class="card mb-4">
                        <div class="card-header d-flex justify-content-between align-items-center pb-2 p-2 pt-3">
                            <h5 class="mb-0">Application History</h5>
                            
                        </div>
                        <div class="card-body p-1">
                            <div class="" id="counts_show">
                            </div>

                            <form method="GET" action="{{ route('application-history.index') }}"
                                class="mb-2 mt-2 p-1 justify-content-end" id="findInTable">

                                <div class="row d-flex" >

                                <div class=" mt-1 mb-1 col-12 col-sm-6 col-md-4 col-xl-3">
                                    <label>Application Id</label>
                                     <input type="text" name="application_id" class="form-control" placeholder="Application Id"
                                        value="">
                                </div>
                                <div class=" mt-1 mb-1 col-12 col-sm-6 col-md-4 col-xl-3">
                                    <label>Passport No</label>
                                     <input type="text" name="passport_no" class="form-control" placeholder="Search for intakes"
                                        value="">
                                </div>
                                <div class=" mt-1 mb-1 col-12 col-sm-6 col-md-4 col-xl-3">
                                    <label>Student Name</label>
                                     <input type="text" name="student_name" class="form-control" placeholder="Search for intakes"
                                        value="">
                                </div>

                                 <div class=" mt-1 mb-1 col-12 col-sm-6 col-md-4 col-xl-3">
                                  <label>University Name</label>
                                    <select class="form-control select2" name="university_id">
                                    <option value="">Select University</option> 
                                    @foreach($universities as $key => $value)
                                        <option value="{{$value['id']}}">{{$value['name']}}</option> 
                                    @endforeach
                                   
                                    </select>
                                </div>
                               
                                <div class=" mt-1 mb-1 col-12 col-sm-6 col-md-4 col-xl-3">
                                 <label>Course Intake</label>
                                    <select class="form-control select2" name="intake_id">
                                    <option value="">Select Intale</option> 
                                    @foreach($intakes as $key => $value)
                                        <option value="{{$value['id']}}">{{$value['name']}}</option> 
                                    @endforeach
                                    </select>
                                </div>
                                <div class=" mt-1 mb-1 col-12 col-sm-6 col-md-4 col-xl-3">
                                <label>Application Status</label>
                                   <select class="form-control select2" name="applicatio_status">
                                    <option value="">Select application status</option> 
                                    @foreach($applicationStage as $key => $value)
                                        <option value="{{$value['id']}}">{{$value['name']}}</option> 
                                    @endforeach
                                    </select>
                                </div>
                                <div class=" mt-1 mb-1 col-12 col-sm-6 col-md-4 col-xl-3">
                                 <label>Assign To</label>
                                     <select class="form-control select2" name="assign_id">
                                    <option value="">Select Assign List</option> 
                                    @foreach($assignList as $key => $value)
                                        <option value="{{$value['id']}}">{{$value['name']}}</option> 
                                    @endforeach
                                    </select>
                                </div>
                               
                                <div class=" mt-1 mb-1 col-12 col-sm-6 col-md-4 col-xl-3 " >
                                <label> </label>
                                    <div class="input-group" >
                                    
                                    <button type="submit" class="btn btn-primary">Search</button>
                                </div>
                                </div>
                                
                                </div>
                            </form>
                            <div class="row">
                                <div >
                                    <button id="create-all-filters" class="btn btn-success float-end" style="display: none;">Create All Filters</button>
                                </div>
                            </div>
                            <div class="table-responsive mt-1" >
                                
                                <div id="intakes_table">
                                    <div class="w-100 d-flex">
                                        @include('admin-panel.components.loader')
                                    </div>
                                </div>
                                <div id="pagination-controls">
                                   
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

    <script>
        $(document).ready(function() {
    function loadRoles(filter = null, var_name = null) {
        $('#intakes_table').html(`
            <div class="w-100 d-flex">
                @include('admin-panel.components.loader')
            </div> 
        `);

        var formData = $('#findInTable').serialize(); // Serialize form data
        if (filter !== null && var_name !== null) {
            formData += '&' + encodeURIComponent(var_name) + '=' + encodeURIComponent(filter);
        }

        const filterButtons = document.querySelectorAll('.active-filter');
        filterButtons.forEach(button => {
            const id = button.getAttribute('data-id');
            const variableName = button.getAttribute('data-varibale_name');
            const text = button.textContent.trim(); // Gets the text inside the button
            formData += '&' + encodeURIComponent(variableName) + '=' + encodeURIComponent(id);
        });

        $.ajax({
            url: '{{ route('application-history.fetch') }}', // Adjust this route as needed
            type: 'GET',
            data: formData,
            success: function(response) {
                $('#intakes_table').html(response.html);
                $('#pagination-controls').html(response.pagination);

                // Check if any filter is selected and show the button accordingly
                checkFilters();
            },
            error: function(xhr) {
                $('#intakes_table').html(`
                    <div class="w-100 d-flex">
                        @include('admin-panel.components.loader')
                    </div> 
                `);
            }
        });
    }

    function checkFilters() {
        const filterFields = $('#findInTable').find('input, select');
        let anyFilterSelected = false;

        filterFields.each(function() {
            if ($(this).val()) {
                anyFilterSelected = true;
                return false; // Break the loop
            }
        });

        if (anyFilterSelected) {
            $('#create-all-filters').show();
        } else {
            $('#create-all-filters').hide();
        }
    }

    $('#findInTable').on('submit', function(e) {
        e.preventDefault();
        loadRoles();
    });
    loadRoles();

          

    function CountData() {
        $('#counts_show').html(`
            <tr>
                <td colspan="100">
                    <div class="w-100 d-flex">
                        @include('admin-panel.components.loader')
                    </div> 
                </td>
            </tr>
        `);

        $.ajax({
            url: '{{ route('application-history.CountData') }}', // Adjust this route as needed
            type: 'GET',
            success: function(response) {
                $('#counts_show').html(response.html);
            },
            error: function(xhr) {
                $('#counts_show').html(`
                    <tr>
                        <td colspan="100">
                            <div class="w-100 d-flex">
                                Error occurred. Please try again.
                            </div> 
                        </td>
                    </tr>
                `);
            }
        });
    }
    CountData();

    $(document).on('click', '.filter-btn', function() {
        let intakeId = $(this).data('id');
        let var_name = $(this).data('varibale_name');
        $('.filter-btn').removeClass('active-filter').css({
            'box-shadow': ''
        });
        $(this).addClass('active-filter').css({
            'box-shadow': 'inset 0 0 0 2px blue'
        });
        loadRoles(intakeId, var_name);
    });

    $(document).on('click', '.pagignation-btn', function() {
        let intakeId = $(this).data('id');
        let var_name = $(this).data('varibale_name');
        $('.pagignation-btn').removeClass('active-pagignation').css({
            'box-shadow': ''
        });
        $(this).addClass('active-pagignation').css({
            'box-shadow': 'inset 0 0 0 2px blue'
        });
        loadRoles(intakeId, var_name);
    });

    // Initial check to see if any filters are selected when the page loads
    checkFilters();

     $('#create-all-filters').on('click', function() {
      // Clear form fields
        $('#findInTable')[0].reset();
        // Reset Select2 elements to default
        $('#findInTable').find('select').each(function() {
            $(this).val(null).trigger('change'); // Reset Select2 to default
        });
        // Reload the data with no filters applied
        loadRoles();
        // Hide the "Create All Filters" button
        $('#create-all-filters').hide();
        
    });
});

    </script>
@endsection
