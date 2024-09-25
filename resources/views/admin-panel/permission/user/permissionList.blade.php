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

<div class="container">
    <div class="row">
        @if(count($Permissions) <= 0)
            <div class="alert alert-warning">
                <span>No permissions available.</span>
            </div>
        @else
            @foreach($Permissions as $groupName => $permissions)
                <div class="col-12 col-md-6 col-lg-4">
                    <div class="permission-card">
                        <div class="permission-card-header">
                            <input type="checkbox" class="role-checkbox" data-group="{{ $groupName ?? 'Default Group' }}"> 
                            <span class="" style="margin-left: 10px;"> {{ $groupName ?? 'Default Group' }} </span>
                        </div>
                        <div class="permission-card-body">
                            <ul class="list-group list-group-flush">
                                @foreach($permissions as $permission)
                                    <li class="list-group-item permission-list-item">
                                        <input type="checkbox" class="permission-checkbox" {{ in_array($permission['id'], $existingPermissions) ? 'checked' : '' }} name="{{ $permission['id'] }}" data-group="{{ $groupName ?? 'Default Group' }}">
                                        <span> {{ $permission['name'] }} </span>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            @endforeach
        @endif
    </div>
</div>
