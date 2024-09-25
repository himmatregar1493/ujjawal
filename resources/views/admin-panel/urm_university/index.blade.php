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

    .hide_data {
        display: none;
    }

    #error_message {
        display: none;
        color: red;
    }

</style>

<div class="content-wrapper">
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="row">
            <div class="col-xl">
                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">URM University</h5>
                    </div>
                    <div class="card-body">
                        <form id="urm_university_form" method="POST">
                            @csrf
                            <div class="row">
                                <div class="mt-1 mb-1 col-12 col-md-6 col-lg-4 col-xl-4">
                                    <label class="form-label required-label" for="university_id">University Name</label>
                                    <select class="form-control select2" name="university_id" id="university_id">
                                        <option value="">Select University Name</option>
                                        @foreach($university_list as $item)
                                        <option value="{{ $item['id'] }}">
                                            {{ $item['name'] }}
                                        </option>
                                        @endforeach
                                    </select>
                                    @error('university_id')
                                    <div class="text-danger mt-1">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mt-1 mb-1 col-12 col-md-6 col-lg-4 col-xl-4">
                                    <label class="form-label required-label" for="urm_name">Urm Name</label>
                                    <select class="form-control select2" name="urm_name" id="urm_name">
                                        <option value="">Select Urm Name</option>
                                        @foreach($university_list as $item)
                                        <option value="{{ $item['urm_name'] }}">
                                            {{ $item['urm_name'] }}
                                        </option>
                                        @endforeach
                                    </select>
                                    @error('urm_name')
                                    <div class="text-danger mt-1">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <p id="error_message"></p>
                                <div class="mt-2 mb-1 col-12 col-md-12 col-lg-12 col-xl-12">
                                    <button type="button" onclick="GetDetailData()" class="btn btn-primary mt-1">Search</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <div id="urm_university_list">
            <!-- Content will be loaded here -->
        </div>
        <div id="pagination-controls">
            
        </div>
    </div>
</div>



<script>

function GetDetailData(filter = null,var_name = null) {
                $('#universitys_table').html(`
                    
                            <div class="w-100 d-flex">
                                @include('admin-panel.components.loader')
                            </div> 
                        
                `);
                

                var formData = $('#urm_university_form').serialize(); // Serialize form data
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
                    url: '{{ route('urm_university.fetch') }}', // Adjust this route as needed
                    type: 'GET',
                    data: formData,
                    success: function(response) {
                         $('#universitys_table').html(response.html);
                       $('#pagination-controls').html(response.pagination);
                        
                    },
                    error: function(xhr) {
                        $('#universitys_table').html(`
                            <div class="w-100 d-flex">
                                @include('admin-panel.components.loader')
                            </div> 
                        `);
                    }
                });
            }
            GetDetailData();
             $('#urm_university_form').on('submit', function(e) {
                e.preventDefault();
                GetDetailData();
            });

    function GetDetailData(page = 1) {
        $('#urm_university_list').html(`
            <div class="card w-100">
                @include('admin-panel.components.loader')
            </div>
        `);

        var formData = $('#urm_university_form').serialize(); // Serialize form data

        $.ajax({
            url: '{{ route('urm_university.fetch') }}', 
            type: 'POST', 
            data: formData + '&page=' + page,
            success: function(response) {
                $('#urm_university_list').html(response.html);
                $('#pagination-controls').html(response.pagination);
            },
            error: function(xhr) {
                $('#urm_university_list').html("Error Occurred. Please try again.");
            }
        });
    }

    $(document).on('click', '.pagignation-btn', function() {
        let id = $(this).data('id');
        let var_name = $(this).data('varibale_name');
        $('.pagignation-btn').removeClass('active-pagignation').css({
            'box-shadow': ''
        });
        $(this).addClass('active-pagignation').css({
            'box-shadow': 'inset 0 0 0 2px blue'
        });
        GetDetailData(id,var_name);
    });
</script>
@endsection

