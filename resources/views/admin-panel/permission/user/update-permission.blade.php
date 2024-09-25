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
        display: block;
        margin: auto;
    }

    #detail-page {
        display: flex;
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
        display: flex;
        align-items: center;
    }

    .permission-card-body {
        padding: 1rem;
    }

    .permission-list-item {
        border-radius: 5px;
        margin-bottom: 0.5rem;
        display: flex;
        align-items: center;
    }

    .permission-checkbox {
        margin-right: 0.5rem;
    }

</style>

<div class="content-wrapper">
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="row">
            <div class="col-xl">
                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center pb-2 p-2 pt-3">
                        <h5 class="mb-0">User Permission</h5>
                        <a href="{{ route('permission.index') }}">
                            <button id="create-permission-btn" class="mb-0 btn btn-primary">Back</button>
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

                        <form id="apply-course-form" method="POST" action="{{ route('permission.store') }}">
                            @csrf
                            <div class="row">
                                <div class="mt-1 mb-1 col-12 col-md-6 col-lg-4 col-xl-4">
                                    <label class="form-label required-label" for="role">Select Role</label>
                                    <select class="form-control select2" id="selectRole" multiple name="role[]">
                                        @foreach($roles as $key => $value)
                                            <option value="{{ $value['id'] }}">{{ $value['name'] }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div id="update_permission" class="mt-4"></div>
                                <div class="mt-2 mb-1 col-12">
                                    <button type="submit" class="btn btn-primary mt-1">Save</button>
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
        // Initialize Select2
        $('.select2').select2();

        // Function to get permissions via AJAX
        function getPermission() {
            var roles = $('#selectRole').val();
            $.ajax({
                url: '{{ route('permission.get_user_permission') }}', // Adjust this route as needed
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}', // Include CSRF token
                    roles: roles
                },
                success: function(response) {
                    $('#update_permission').html(response.html);
                },
                error: function() {
                    $('#update_permission').html(`
                        <div class="w-100 d-flex">
                            Error occurred. Please try again.
                        </div>
                    `);
                }
            });
        }

        // Call `getPermission` function when select element changes
        $('#selectRole').on('change', function() {
            getPermission();
        });

        // Update parent checkboxes based on children
        function updateParentCheckbox() {
            $('.role-checkbox').each(function() {
                const groupName = $(this).data('group');
                const associatedPermissions = $(`.permission-checkbox[data-group="${groupName}"]`);

                const allChecked = associatedPermissions.length > 0 && associatedPermissions.length === associatedPermissions.filter(':checked').length;
                const anyChecked = associatedPermissions.is(':checked');

                $(this).prop('checked', allChecked).prop('indeterminate', !allChecked && anyChecked);
            });
        }

        // Event listener for role checkboxes to update all related permission checkboxes
        $(document).on('change', '.role-checkbox', function () {
            const isChecked = $(this).is(':checked');
            const groupName = $(this).data('group');

            $(`.permission-checkbox[data-group="${groupName}"]`).prop('checked', isChecked);

            updateParentCheckbox();
        });

        // Event listener for permission checkboxes to update the related role checkbox
        $(document).on('change', '.permission-checkbox', function () {
            updateParentCheckbox();
        });

        // Initial update to set the correct state of parent checkboxes
        updateParentCheckbox();
    });
</script>
@endsection
