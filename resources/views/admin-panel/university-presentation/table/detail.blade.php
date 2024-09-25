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

    @if (count($university_presentations) <= 0)
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
                    <th>Name</th>
                    <th>Status</th>
                    <th>University Name</th>
                    <th>Created At</th>

                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @if (count($university_presentations) <= 0)
                 <div class="card-body d-flow" style="text-align:center;">
                    No record Found
                </div>
                    @else
                    @foreach ($university_presentations as $item)
                    <tr>
                        <td>{{ $item['id'] }}</td>
                        <td><a title="click to view file" target="_blank" href="{{ asset('admin_assets\images\visatool_image') }}/{{ $item['link'] }}">{{ $item['name'] }}</a></td>
                        <td id="status{{$item['id']}}">
                            <div class="form-check form-switch">
                                <input class="form-check-input" onchange="change_status('status{{$item['id']}}',{{$item['id']}})" type="checkbox" {{ $item['is_active'] ? 'checked' : '' }}>
                            </div>
                        </td>
                        <td>{{ $item['university_name'] }}</td>
                        
                        <td>{{ DateTimeFormate($item['created_at']) }}</td>

                        <td>
                            <a class="btn btn-primary btn-sm edit-intake-btn" href="{{ route('university-presentation.edit', $item['id']) }}">
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
        @if (count($university_presentations) <= 0)

        <div class="card-body d-flow" style="text-align:center;">
            No record Found
            </div>

        @else

            @foreach ($university_presentations as $item)
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
                         {{ $item['name'] }}
                        </div>
                    </div>
                     <div class="col-12 col-xl-12 d-flex">
                        <div class="col-6 col-xl-6">
                        Status:
                        </div>
                        <div class="col-6 col-xl-6">
                         <div class="form-check form-switch">
                                <input class="form-check-input" onchange="change_status('status{{$item['id']}}',{{$item['id']}})" type="checkbox" {{ $item['is_active'] ? 'checked' : '' }}>
                            </div>
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
                        <a class="btn btn-primary btn-sm edit-intake-btn" href="{{ route('university-presentation.edit', $item['id']) }}">
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
                url: '{{ route('university-presentation.change_status') }}', // Adjust this route as needed
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
                url: '{{ route('university-presentation.CountData') }}',
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
