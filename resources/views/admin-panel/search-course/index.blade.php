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
                            <h5 class="mb-0">Course Finder</h5>
                            {{-- <a href="{{Route('search-course.create')}}"><button id="create-university-btn" class="mb-0 btn btn-primary">Create</button></a> --}}
                        </div>
                        <div class="card-body p-1">
                           

                            <form method="GET" action="{{ route('search-course.index') }}"
                                class="mb-2 mt-2 d-flex  " id="findInTable">
                                
                                <div class="col-12 col-md-6 col-lg-4 col-xl-3" style="border:1px solid #ded4d4; padding:10px; border-radius:10px 0px 0px 10px; background:aliceblue;">
                                    <div class="mt-3 mb-3">
                                        <label>University Select </label>
                                        <select class="form-control select2" name="university_id">
                                        <option value="">Select University</option>
                                        @foreach($universities as $item)
                                            <option value="{{$item['id']}}">{{$item['name']}}</option>
                                        @endforeach
                                        
                                        </select>
                                    </div>
                                    <div class="mt-3 mb-3">
                                        <label>Course Type</label>
                                        <select class="form-control select2" name="course_type">
                                        <option value="">Select Course Type</option>
                                        @foreach($courseTypes as $key => $item)
                                            <option value="{{$key}}">{{$item}}</option>
                                        @endforeach
                                        
                                        </select>
                                    </div>
                                    <div class="mt-3 mb-3">
                                        <label>Course Intake </label>
                                        <select class="form-control select2" name="intake">
                                        <option value="">Select Intake</option>
                                         @foreach($courseIntake as $item)
                                            <option value="{{$item['id']}}">{{$item['name']}}</option>
                                        @endforeach
                                        </select>
                                    </div>
                                    {{-- <div class="mt-3 mb-3">
                                        <label>Placement Select </label>
                                        <select class="form-control select2">
                                        <option value="">Select University</option>
                                        <option value="">h</option>
                                        </select>
                                    </div> --}}
                                    <div class="mt-3 mb-3">
                                        <label>Academic Entry Requirement </label>
                                        <select class="form-control select2" name="aca_entry_requirement">
                                        <option value="">Select Academic Entry Requirement</option>
                                       @foreach($accademic_entry_requirement as $key => $item)
                                            <option value="{{$key}}">{{$item}}</option>
                                        @endforeach
                                        </select>
                                    </div>
                                    <div class="mt-3 mb-3">
                                        <label>Location</label>
                                        <select class="form-control select2" name="location">
                                        <option value="">Select Location</option>
                                         @foreach($locations as $item)
                                            <option value="{{$item['location']}}">{{$item['location']}}</option>
                                        @endforeach
                                        </select>
                                    </div>
                                    {{-- <div class="mt-3 mb-3">
                                        <label>English Requirement</label>
                                        <select class="form-control select2">
                                        <option value="">Select University</option>
                                        <option value="">h</option>
                                        </select>
                                    </div> --}}
                                    <div class="mt-3 mb-3">
                                        <label>English Waiver</label>
                                        <select class="form-control select2" name="english_waiver">
                                        <option value="">Select English Waiver</option>
                                       @foreach($englishWaiver as $key =>  $item)
                                            <option value="{{$key}}">{{$item}}</option>
                                        @endforeach
                                        </select>
                                    </div>
                                    {{-- <div class="mt-3 mb-3">
                                        <label>Top-up or Lateral Entry</label>
                                        <select class="form-control select2">
                                        <option value="">Select University</option>
                                        <option value="">h</option>
                                        </select>
                                    </div> --}}
                                    <div class="mt-3 mb-3">
                                        <label>Student PTE Score </label>
                                            <input 
                                                    type="text" 
                                                    name="pte_score" 
                                                    id="pte_score" 
                                                    class="form-control" 
                                                    min="0" 
                                                    max="100" 
                                                    step="1" 
                                                    placeholder="Enter score between 0 and 100"
                                                >
                                    </div>

                                    <div class="mt-3 mb-3">
                                        <label>Student IELTS Score</label>
                                            <input 
                                                    type="text" 
                                                    name="ielts_score" 
                                                    id="ielts_score" 
                                                    class="form-control" 
                                                    min="0" 
                                                    max="100" 
                                                    step="1" 
                                                    placeholder="Enter score between 0 and 100"
                                                >
                                    </div>
                                    
                                    {{-- <div class="mt-3 mb-3">
                                        <label>Field Of study </label>
                                        <select class="form-control select2">
                                        <option value="">Select University</option>
                                        <option value="">h</option>
                                        </select>
                                    </div> --}}
                                </div>
                                <div class="col-12 col-md-6 col-lg-8 col-xl-9" style="border:1px solid #ded4d4;  border-radius:0px 10px 10px 0px;">
                                <div class="col-12 col-xl-12 justify-content-end d-flex" style="border-bottom:1px solid #ded4d4; padding:10px;">
                                    <div class="input-group" style="width: 300px; justify-content-end">
                                        <input type="text" name="search" class="form-control" placeholder="Search for universitys"
                                            value="{{ request()->query('search') }}">
                                        <button type="submit" class="btn btn-primary">Search</button>
                                    </div>
                                </div>
                                    <div id="universitys_table" style="padding:10px;">
                                    <div class="w-100 d-flex">
                                        @include('admin-panel.components.loader')
                                    </div>
                                </div>
                                <div id="pagination-controls">
                                   
                                </div>
                                </div>

                              
                            

                           
                        </form>
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
           $('#universitys_table').html(`
                <div class="w-100 d-flex">
                    @include('admin-panel.components.loader')
                </div>
            `);

            $('#pagination-controls').hide();
           

            var formData = $('#findInTable').serialize(); // Serialize form data
            if (filter !== null && var_name !== null) {
                formData += '&' + encodeURIComponent(var_name) + '=' + encodeURIComponent(filter);
            }

            const filterButtons = document.querySelectorAll('.active-filter');
            filterButtons.forEach(button => {
                const id = button.getAttribute('data-id');
                const variableName = button.getAttribute('data-varibale_name');
                formData += '&' + encodeURIComponent(variableName) + '=' + encodeURIComponent(id);
            });

            $.ajax({
                url: '{{ route('search-course.fetch') }}', // Adjust this route as needed
                type: 'GET',
                data: formData,
                success: function(response) {
                    $('#universitys_table').html(response.html);
                    $('#pagination-controls').html(response.pagination);
                     $('#pagination-controls').show();
                },
                error: function(xhr) {
                  $('#universitys_table').html(`
                    <div class="w-100 d-flex">
                        Error 
                    </div>
                `);
                $('#pagination-controls').hide();
                }
            });
        }

        // Load roles on page load
        loadRoles();

        // Handle form submission
        $('#findInTable').on('submit', function(e) {
            e.preventDefault();
            loadRoles();
        });

        // Handle select change events
        $('#findInTable select').on('change', function() {
            loadRoles(); // Trigger the AJAX request when any select dropdown changes
        });

      

        // Filter button click handler
        
        // Pagination button click handler
        $(document).on('click', '.pagignation-btn', function() {
            let universityId = $(this).data('id');
            let var_name = $(this).data('varibale_name');
            $('.pagignation-btn').removeClass('active-pagignation').css({
                'box-shadow': ''
            });
            $(this).addClass('active-pagignation').css({
                'box-shadow': 'inset 0 0 0 2px blue'
            });
            loadRoles(universityId, var_name);
        });
    });



    $('#pte_score,#ielts_score').on('input', function() {
    let value = $(this).val();
    
    // Remove non-numeric characters
    value = value.replace(/[^0-9]/g, '');
    
    // Convert value to a number
    let number = parseInt(value, 10);

    // If the parsed number is NaN, reset to empty
    if (isNaN(number)) {
        $(this).val('');
    } else {
        // Check if the number is within the allowed range
        if (number < 0) {
            number = 0;
        } else if (number > 100) {
            number = 100;
        }

        // Update the input value
        $(this).val(number);
    }
});

</script>
@endsection
