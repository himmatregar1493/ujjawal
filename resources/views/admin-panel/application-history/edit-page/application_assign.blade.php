<style>
    .center-container {
        display: flex;
        justify-content: center;
        align-items: center;
        height: 100px;
    }

    .status-toggle {
        cursor: pointer;
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

        .table-responsive {
            margin-top: 20px;
            padding: 14px !important;
        }

        .table th,
        .table td {
            vertical-align: middle;
        }
    </style>

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
    td{
        color:black !important;
    }

</style>

<div class="mb-3" style="background:#d5dfe8; border-radius:15px; margin-bottom: 30px !important; min-height:50px;">
<div class="detail-table">
    @if ($applicationAsignList->count())
    <table class="table " style>
        <thead>
            <tr>
                <th>Assigned By</th>
                <th>Assigned To</th>
                <th>Reason</th>
                <th>Disable Reason</th>
                <th>Status</th>
                <th>Date</th>
              
            </tr>
        </thead>
        <tbody>
            @foreach($applicationAsignList as $assignment)
            <tr data-id="{{ $assignment->id }}">
                <td>{{ $assignment->assign_by }}</td>
                <td>{{ $assignment->assign_to }}</td>
                <td>{{ $assignment->assign_reason }}</td>
                <td>{{ $assignment->disable_assign_reason }}</td>
                
                <td id="status{{$assignment['id']}}">
                    @if(UserCan('application.assign_status_change'))
                        @if($assignment['is_active'] == 1)
                                <div class="form-check form-switch">
                                    <input class="form-check-input" onchange="change_status_assignee('status{{$assignment['id']}}','{{$assignment['id']}}')" type="checkbox" {{ $assignment['is_active'] ? 'checked' : '' }}>
                                </div>
                        @else
                            Disable
                        @endif     
                    @else
                        @if($assignment['is_active'] == 1)
                            Active
                        @else
                            Disable
                        @endif 
                    @endif
                </td>
                <td>
                    {{DateTimeFormate($assignment['created_date'])}}
                </td>
                
            </tr>
            @endforeach
        </tbody>
    </table>
    @else
    <div class="center-container">
        <p class="mt-1">No data found.</p>
    </div>
    @endif
</div>
</div>

<div class="detail-card">
    @if ($applicationAsignList->count() > 0)
        @foreach ($applicationAsignList as $item)
            <div class="row pt-3 pb-3 mt-1 mb-1" style="margin-top: -10px; margin-bottom: 10px; border: 1px solid black; border-radius: 10px;">
                <div class="col-12 d-flex">
                    <div class="col-6">
                        Role ID:
                    </div>
                    <div class="col-6">
                        {{ $item['id'] }}
                    </div>
                </div>
                <div class="col-12 d-flex">
                    <div class="col-6">
                        Name:
                    </div>
                    <div class="col-6">
                        {{ $item['name'] }}
                    </div>
                </div>
                <div class="col-12 d-flex">
                    <div class="col-6">
                        Status:
                    </div>
                    <div class="col-6">
                        <div class="form-check form-switch">
                            <input class="form-check-input" onchange="change_status('status{{ $item['id'] }}', {{ $item['id'] }})" type="checkbox" {{ $item['is_active'] ? 'checked' : '' }}>
                        </div>
                    </div>
                </div>
                <div class="col-12 d-flex">
                    <div class="col-6">
                        Created Date:
                    </div>
                    <div class="col-6">
                        {{ DateTimeFormate($item['created_at']) }}
                    </div>
                </div>
                <div class="col-12 d-flex">
                    <div class="col-6">
                        Action:
                    </div>
                    <div class="col-6">
                        <a class="btn btn-primary btn-sm edit-intake-btn" href="{{ route('course.edit', $item['id']) }}">
                            <img src="{{ asset('assets/img/icons/crud_icon/edit.png') }}" style="width: 15px;">
                        </a>
                    </div>
                </div>
            </div>
        @endforeach
    @else
        <div class="card-body d-flow text-center">
            No record Found
        </div>
    @endif
</div>
@if(UserCan('application.assign'))

<form id="assignForm" action="{{ route('application-history.assign_application_save') }}" method="POST">
    @csrf
    <div class="row" style="background:aliceblue; border-radius:15px; margin:-17px; margin-bottom: -23px; padding-bottom: 16px;">
        <div class="mt-1 mb-1 col-12 col-sm-12 col-md-4 col-xl-4">
            <label for="assignType" class="form-label">Assign To</label>
            <select id="assignType" name="assignType" class="form-select Select2" required>
                <option value="">Select a type</option>
                @foreach($assignList as $key => $value)
                <option value="{{ $value['id'] }}">{{ $value['name'] }}</option>
                @endforeach
            </select>
            <div class="invalid-feedback">
                Please select a comment type.
            </div>
        </div>

        <div class="mt-1 mb-1 col-12 col-sm-12 col-md-6 col-xl-6">
            <label for="assign_reason" class="form-label">Comment/Reason:</label>
            <textarea id="assign_reason" class="form-control" name="assign_reason" rows="1" required></textarea>
            <div class="invalid-feedback">
                Please enter a comment.
            </div>
        </div>

        <div class="mt-1 mb-1 col-12 col-sm-12 col-md-12 col-xl-2">
            <label for="comment" class="form-label">&nbsp;</label><br>
            <button type="submit" class="btn btn-primary">Submit</button>
        </div>
    </div>
</form>

@endif

<script>
    $(document).ready(function() {
        $('.Select2').select2();

        $('#assignForm').on('submit', function(event) {
            event.preventDefault();

            // Get form elements
            const $form = $(this);
            const $comment = $('#assign_reason');
            const $assignType = $('#assignType');

            // Clear previous validation states
            $form.removeClass('was-validated');
            $comment.removeClass('is-invalid');
            $assignType.removeClass('is-invalid');

            let valid = true;

            // Validate comment
            if ($.trim($comment.val()) === '') {
                $comment.addClass('is-invalid');
                valid = false;
            }

            // Validate comment type
            if ($.trim($assignType.val()) === '') {
                $assignType.addClass('is-invalid');
                valid = false;
            }

            if (!valid) {
                $form.addClass('was-validated');
                return;
            }

            // Create an object to send to the server
            const data = {
                assign_reason: $.trim($comment.val()),
                assign_to: $assignType.val(),
                application_id: @json($application_id) // Use Blade's json_encode helper to embed PHP variable
            };

            // Show loading alert
            Swal.fire({
                title: 'Loading...',
                text: 'Please wait while we process your request.',
                icon: 'info',
                showConfirmButton: false,
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading(); // Show the spinner
                }
            });

            // Send data to the server using AJAX
            $.ajax({
                url: $(this).attr('action'),
                type: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') // Ensure the CSRF token is included
                },
                data: data,
                success: function(response) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: response.message || 'Comment saved successfully!',
                        confirmButtonText: 'OK'
                    });
                    // Refresh the list or perform any other action
                    assign_application(); // Assuming you have this function defined to refresh the comments
                },
                error: function(xhr) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: 'Something went wrong!',
                        confirmButtonText: 'OK'
                    });
                }
            }).always(function() {
                Swal.close(); 
            });
        });
    });

    // Define the function in the global scope
    function change_status_assignee(element_id, id) {
    console.log("element_id:", element_id);
    console.log("id:", id);
    
    Swal.fire({
        title: 'Enter Reason',
        input: 'textarea',
        inputLabel: 'Reason for disabling',
        inputPlaceholder: 'Type your reason here...',
        inputAttributes: {
            'aria-label': 'Type your reason here'
        },
        showCancelButton: true,
        confirmButtonText: 'Submit',
        cancelButtonText: 'Cancel',
        showLoaderOnConfirm: true,
        preConfirm: (inputValue) => {
            if (!inputValue) {
                Swal.showValidationMessage('Please enter a reason.');
                return false;
            }
            return $.ajax({
                url: '{{ route('application-history.update_status_assignee') }}', // Adjust this route as needed
                type: 'POST',
                data: {
                    id: id,
                    reason: inputValue,
                    _token: '{{ csrf_token() }}'
                }
            }).done(function(response) {
                if (response.status === 'success') {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success',
                        text: response.message
                    });
                    assign_application();
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: response.message
                    });
                }
            }).fail(function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Failed to submit reason.'
                });
            });
        }
    });
}
</script>
