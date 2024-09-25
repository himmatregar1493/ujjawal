<style>
    @media (max-width: 700px) {
        .detail-table{
            display:none;
        }
        .detail-card{
            display:inline;
        }
    }

    @media (min-width: 700px) {
        .detail-table{
            display:inline;
        }
        .detail-card{
            display:none;
        }
    }

</style>

    @if (count($applications) <= 0) 
         <div class="card-body d-flow" style="text-align:center;">
            No record Found
            </div>
    @else
    <div class="detail-table">
        <table class="table">
            <!-- Table Header and Body -->
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Student Name</th>
                    <th>institute Name</th>
                    <th>Course Name</th>
                    <th>Application Status</th>
                    <th>Date Added</th>
               
               
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @if (count($applications) <= 0) 
                 <div class="card-body d-flow" style="text-align:center;">
            No record Found
            </div>
                    @else
                    @foreach ($applications as $item)
                    <tr>
                        <td>{{ $item['id'] }}</td>
                        <td>
                        {{ $item['student_first_name'] }} {{ $item['student_last_name'] }}<br>
                        <b>Passport No:</b> : {{ $item['student_passport'] }}<br>
                        <b>Counsellor</b> : {{ $item['counseller_name'] }}<br>
                        </td>
                        <td>
                                {{ $item['institute_name'] }}<br>
                            @if(!empty($item['university_name']))
                                <b title="University Name">U Name : </b> {{ $item['university_name'] }}<br>
                            @endif
                            
                            @if(!empty($item['urm_name']))
                                <b>URM Name : </b>  {{ $item['urm_name'] }}<br>
                            @endif

                            @if(!empty($item['urm_contact_no']))
                                <b>URM No : </b> {{ $item['urm_contact_no'] }}<br>
                            @endif
                        </td>
                        <td>{{ $item['course_name'] }}</td>
                        <td>{{ $item['application_stage'] }}</td>
                        <td>{{ DateTimeFormate($item['created_at']) }}</td>
                       
                        <td>
                            <a class="btn btn-primary btn-sm edit-intake-btn" href="{{ route('application-history.edit', $item['id']) }}">
                            <img src="{{ asset('assets/img/icons/crud_icon/edit.png') }}" style="width:15px">
                        </a>
                            
                        </td>
                    </tr>
                    @endforeach
                    @endif
            </tbody>
        </table>
    </div>

    <div class="detail-card" >
        @if (count($applications) <= 0) 
        
        <div class="card-body d-flow" style="text-align:center;">
            No record Found
            </div>
    
        @else
    
            @foreach ($applications as $item)
                <div class="row pt-3 pb-3 mt-1 mb-1" style=" margin-top:-10px;  margin-bottom:10px; border:1px solid black; border-radius:10px ">
                    <div class="col-12 col-xl-12 d-flex">
                        <div class="col-6 col-xl-6">
                        ID:
                        </div>
                        <div class="col-6 col-xl-6">
                         {{ $item['id'] }}
                        </div>
                    </div>
                     <div class="col-12 col-xl-12 d-flex">
                        <div class="col-6 col-xl-6">
                        Name:
                        </div>
                        <div class="col-6 col-xl-6">
                        <td>{{ $item['student_first_name'] }} {{ $item['student_last_name'] }}</td>
                        </div>
                    </div>
                     
                     <div class="col-12 col-xl-12 d-flex">
                        <div class="col-6 col-xl-6">
                        Created Date:
                        </div>
                        <div class="col-6 col-xl-6">
                        {{ DateTimeFormate($item['created_at']) }}
                        </div>
                    </div>
                     
                     <div class="col-12 col-xl-12 d-flex">
                        <div class="col-6 col-xl-6">
                        Action:
                        </div>
                        <div class="col-6 col-xl-6">
                        <a class="btn btn-primary btn-sm edit-intake-btn" href="{{ route('application-history.edit', $item['id']) }}">
                            <img src="{{ asset('assets/img/icons/crud_icon/edit.png') }}" style="width:15px">
                        </a>
                        
                        </div>
                    </div>
                    
                </div>
            @endforeach
        @endif
    @endif
   
    <script>
        function change_status(element_id, id) {
            // Show loading indicator while processing the request
            $('#' + element_id).html(`
                <div class="w-100 d-flex">
                    @include('admin-panel.components.loader')
                </div>
            `);

            // Prepare the form data
            var formData = new FormData();
            formData.append('id', id);

            // Perform the AJAX request
            $.ajax({
                url: '{{ route('application-stage.change_status') }}', // Adjust this route as needed
                type: 'POST'
                , data: formData
                , processData: false, // Required for FormData
                contentType: false, // Required for FormData
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}' // Include CSRF token if using Laravel
                }
                , success: function(response) {
                    if (response.success) {
                        Swal.close();
                        Swal.fire({
                            icon: 'success'
                            , title: 'Success!'
                            , text: response.message
                            , confirmButtonText: 'OK'
                        });

                        $('#' + element_id).html(`
                    <div class="form-check form-switch">
                        <input class="form-check-input" onchange="change_status('${element_id}', ${id})" type="checkbox" ${response.status ? 'checked' : ''}>
                    </div>
                `);
                        CountData();
                    } else {
                        // Handle any errors returned from the server
                        Swal.close();
                        Swal.fire({
                            icon: 'error'
                            , title: 'Error!'
                            , text: response.message
                            , confirmButtonText: 'OK'
                        });
                        $('#' + element_id).html(`
                    <div class="w-100 d-flex">
                        Error occurred. Please try again.
                    </div>
                `);
                    }
                }
                , error: function(xhr) {
                    // Handle client-side errors
                    $('#' + element_id).html(`
                <div class="w-100 d-flex">
                    Error occurred. Please try again.
                </div>
            `);
                }
            });
        }

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
                url: '{{ route('application-stage.CountData') }}',
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

       

    </script>
