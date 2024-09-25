<style>
    .detail-table {
            display: none;
        }

        .detail-card {
            display: none;
        }
    @media (max-width: 700px) {
        .detail-table {
            display: none;
        }

        .detail-card {
            display: inline;
        }
    }

    @media (min-width: 700px) {
        .detail-table {
            display: inline;
        }

        .detail-card {
            display: none;
        }
    }
    .quentity{
        color: rgb(255, 255, 255) !important;
        padding: 5px;
        border-radius:10px;
        font-weight: 900;

    }
</style>

@if (count($products) <= 0)
    <div class="card-body d-flow" style="text-align:center;">
        No record Found
    </div>
@else
    <div class="detail-table">
        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Remaining Quentity</th>
                    <th>Created At</th>

                    <th>Created By</th>
                    <th>Is Active</th>


                    <th>CGST (%)</th>
                    <th>SGST (%)</th>
                    <th>IGST (%)</th>
                    <th>UTGST (%)</th>
                    <th>Price</th>
                    <th>Product Unit</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($products as $item)
                    <tr >
                        <td>{{ $item['id'] }}</td>
                        <td>{{ $item['name'] }}</td>
                        <td > <span class="quentity" @if($item['remaining_quantity'] > 100) style="background:#9dcd9d;" @elseif ($item['remaining_quantity'] > 50 && $item['remaining_quantity'] <= 100) style="background:#edd07a; color:white;" @elseif ($item['remaining_quantity'] > 5 && $item['remaining_quantity'] <= 50)  style="background:#ffa7a7c7;" @else style="background:red;"  @endif> {{ @$item['remaining_quantity'] }}</span></td>
                        <td>{{ DateTimeFormate($item['created_at']) }}</td>

                        <td>{{ $item['created_by'] }}</td>
                        <td>
                            <div class="form-check form-switch">
                                <input class="form-check-input"
                                    onchange="change_status('status{{ $item['id'] }}',{{ $item['id'] }})"
                                    type="checkbox" {{ $item['is_active'] ? 'checked' : '' }}>
                            </div>
                        </td>


                        <td>{{ $item['cgst'] !== null && $item['cgst'] !== '' ? $item['cgst'].'%' : '' }}</td>
                        <td>{{ $item['sgst'] !== null && $item['sgst'] !== '' ? $item['sgst'].'%' : '' }}</td>
                        <td>{{ $item['igst'] !== null && $item['igst'] !== '' ? $item['igst'].'%' : '' }}</td>
                        <td>{{ $item['utgst'] !== null && $item['utgst'] !== '' ? $item['utgst'].'%' : '' }}</td>

                        <td>{{ $item['price'] }}</td>
                        <td>{{ $item['product_unit'] }}</td>
                        <td>
                            <a class="btn btn-primary btn-sm edit-intake-btn"
                                href="{{ route('product.edit', $item['id']) }}">
                                <img src="{{ asset('assets/img/icons/crud_icon/edit.png') }}" style="width:15px">
                            </a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="detail-card">
        @foreach ($products as $item)
        <div class="row pt-3 pb-3 mt-1 mb-1" style=" margin-top:-10px;  margin-bottom:10px; border:1px solid black; border-radius:10px ">
            <div class="col-12">
                <div class="row">
                    <div class="col-6"><strong>ID:</strong></div>
                    <div class="col-6">{{ $item['id'] }}</div>
                </div>
                <div class="row">
                    <div class="col-6"><strong>Name:</strong></div>
                    <div class="col-6">{{ $item['name'] }}</div>
                </div>
                <div class="row">
                    <div class="col-6"><strong>Remaining Quentity:</strong></div>
                    <div class="col-6">{{ @$item['remaining_quantity'] }}</div>
                </div>
                <div class="row">
                    <div class="col-6"><strong>Created At:</strong></div>
                    <div class="col-6">{{ DateTimeFormate($item['created_at']) }}</div>
                </div>

                <div class="row">
                    <div class="col-6"><strong>Created By:</strong></div>
                    <div class="col-6">{{ $item['created_by'] }}</div>
                </div>
                <div class="row">
                    <div class="col-6"><strong>Is Active:</strong></div>
                    <div class="col-6">
                        <div class="form-check form-switch">
                            <input class="form-check-input" onchange="change_status('status{{ $item['id'] }}',{{ $item['id'] }})" type="checkbox" {{ $item['is_active'] ? 'checked' : '' }}>
                        </div>
                    </div>
                </div>


                <div class="row">
                    <div class="col-6"><strong>CGST (%):</strong></div>
                    <div class="col-6">{{ $item['cgst'] !== null && $item['cgst'] !== '' ? $item['cgst'].'%' : '' }}</div>
                </div>
                <div class="row">
                    <div class="col-6"><strong>SGST (%):</strong></div>
                    <div class="col-6">{{ $item['sgst'] !== null && $item['sgst'] !== '' ? $item['sgst'].'%' : '' }}</div>
                </div>
                <div class="row">
                    <div class="col-6"><strong>IGST (%):</strong></div>
                    <div class="col-6">{{ $item['igst'] !== null && $item['igst'] !== '' ? $item['igst'].'%' : '' }}</div>
                </div>
                <div class="row">
                    <div class="col-6"><strong>UTGST (%):</strong></div>
                    <div class="col-6">{{ $item['utgst'] !== null && $item['utgst'] !== '' ? $item['utgst'].'%' : '' }}</div>
                </div>


                <div class="row">
                    <div class="col-6"><strong>Price:</strong></div>
                    <div class="col-6">{{ $item['price'] }}</div>
                </div>
                <div class="row">
                    <div class="col-6"><strong>Product Unit:</strong></div>
                    <div class="col-6">{{ $item['product_unit'] }}</div>
                </div>
            </div>
        </div>
        @endforeach
    </div>

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
            url: '{{ route('product.change_status') }}', // Adjust this route as needed
            type: 'POST',
            data: formData,
            processData: false, // Required for FormData
            contentType: false, // Required for FormData
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}' // Include CSRF token if using Laravel
            },
            success: function(response) {
                if (response.success) {
                    Swal.close();
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: response.message,
                        confirmButtonText: 'OK'
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
                        icon: 'error',
                        title: 'Error!',
                        text: response.message,
                        confirmButtonText: 'OK'
                    });
                    $('#' + element_id).html(`
                    <div class="w-100 d-flex">
                        Error occurred. Please try again.
                    </div>
                `);
                }
            },
            error: function(xhr) {
                // Handle product-side errors
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
            url: '{{ route('product.CountData') }}',
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
