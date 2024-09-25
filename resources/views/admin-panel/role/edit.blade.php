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

<div class="content-wrapper">
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="row">
            <div class="col-xl">
                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center pb-2 p-2 pt-3">
                        <h5 class="mb-0">Role Edit</h5>
                        <a href="{{ route('role.index') }}">
                            <button id="create-role-btn" class="mb-0 btn btn-primary">Back</button>
                        </a>
                    </div>
                    <div class="card-body pb-2 p-2 pt-3">
                        
                        <form id="apply-course-form" method="POST" action="{{ route('role.update', $role->id) }}">
                            @csrf
                            <div class="row">
                                

                                
                                    @if(count($Permissions) <= 0)
                                    <div class="alert alert-warning">
                                        No permissions available.
                                    </div>
                                    @else
                                    @foreach($Permissions as $groupName => $permissions)
                                    <div class="col-12 col-sm-12 col-md-6 col-lg-4 col-xl-4">
                                        <div class="permission-card">
                                            <div class="permission-card-header">
                                                <input type="checkbox" class="role-checkbox" data-group="{{ $groupName ?? 'Default Group' }}">
                                                {{ $groupName ?? 'Default Group' }}
                                            </div>
                                            <div class="permission-card-body">
                                                <ul class="list-group list-group-flush">
                                                    @foreach($permissions as $permission)
                                                    <li class="list-group-item permission-list-item">
                                                        <input type="checkbox" class="permission-checkbox" {{in_array($permission['id'],$existingPermissions) ? "checked" : ""}} name="{{$permission['id']}}" data-group="{{ $groupName ?? 'Default Group' }}">
                                                        <span>{{ $permission['name'] }}</span>
                                                    </li>
                                                    @endforeach
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                    @endforeach
                                    @endif
                               

                                <div class="mt-2 mb-1 col-12 col-md-12 col-lg-12 col-xl-12">
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
    document.addEventListener('DOMContentLoaded', function () {
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
            roleCheckbox.addEventListener('change', function () {
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
