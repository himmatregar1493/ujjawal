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
                            <h5 class="mb-0">Roles</h5>
                            <a href="{{Route('role.create')}}"><button id="create-role-btn" class="mb-0 btn btn-primary">Create</button></a>
                        </div>
                        <div class="card-body p-1">
                            <div class="" id="counts_show">
                            </div>

                            <form method="GET" action="{{ route('role.index') }}"
                                class="mb-2 mt-2 d-flex justify-content-end" id="findInTable">
                                <div class="input-group" style="width: 300px;">
                                    <input type="text" name="search" class="form-control" placeholder="Search for roles"
                                        value="{{ request()->query('search') }}">
                                    <button type="submit" class="btn btn-primary">Search</button>
                                </div>
                            </form>

                            <div class="table-responsive mt-" >
                                
                                <div id="roles_table">
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
            function loadRoles(filter = null,var_name = null) {
                $('#roles_table').html(`
                    
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
                    url: '{{ route('role.fetch') }}', // Adjust this route as needed
                    type: 'GET',
                    data: formData,
                    success: function(response) {
                         $('#roles_table').html(response.html);
                       $('#pagination-controls').html(response.pagination);
                        
                    },
                    error: function(xhr) {
                        $('#roles_table').html(`
                            <div class="w-100 d-flex">
                                @include('admin-panel.components.loader')
                            </div> 
                        `);
                    }
                });
            }
            loadRoles();

            $('#findInTable').on('submit', function(e) {
                e.preventDefault();
                loadRoles();
            });

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
                    url: '{{ route('role.CountData') }}', // Adjust this route as needed
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
                let roleId = $(this).data('id');
                let var_name = $(this).data('varibale_name');
                $('.filter-btn').removeClass('active-filter').css({
                    'box-shadow': ''
                });
                $(this).addClass('active-filter').css({
                    'box-shadow': 'inset 0 0 0 2px blue'
                });
                loadRoles(roleId,var_name);
            });

            $(document).on('click', '.pagignation-btn', function() {
                let roleId = $(this).data('id');
                let var_name = $(this).data('varibale_name');
                $('.pagignation-btn').removeClass('active-pagignation').css({
                    'box-shadow': ''
                });
                $(this).addClass('active-pagignation').css({
                    'box-shadow': 'inset 0 0 0 2px blue'
                });
                loadRoles(roleId,var_name);
            });

        });

       
    </script>
@endsection
