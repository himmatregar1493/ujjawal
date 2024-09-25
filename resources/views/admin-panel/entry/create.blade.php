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
    </style>

    <div class="content-wrapper">
        <div class="container-xxl flex-grow-1 container-p-y">
            <div class="row">
                <div class="col-xl">
                    <div class="card mb-4">
                        <div class="card-header d-flex justify-content-between align-items-center pb-2 p-2 pt-3">
                            <h5 class="mb-0">Entry Create</h5>
                            <a href="{{ Route('entry.index') }}"><button id="create-client-btn"
                                    class="mb-0 btn btn-primary">Back</button></a>
                        </div>
                        <div class="card-body pb-2 p-2 pt-3">

                            <form id="apply-course-form" method="POST" action="{{ route('entry.store') }}">
                                @csrf
                                <div class="row">
                                    <div class="mt-1 mb-1 col-12 col-md-6 col-lg-4 col-xl-4">
                                        <label class="form-label required-label" for="vendor"> Select Type</label>
                                        <select id="type" name="type" class="form-control" onchange="get_List_of_client_and_vendor()">
                                            <option value="">Select Type</option>
                                            <option value="client">Client</option>
                                            <option value="vendor">Vendor</option>
                                        </select>
                                    </div>

                                    <div class="mt-1 mb-1 col-12 col-md-6 col-lg-4 col-xl-4" id="vendor-client-container" style="display: none;">
                                        <label id="vendor-client-label" class="form-label required-label"> Select </label>
                                        <select id="vendor-client-list" name="vendor_client" class="form-control">
                                            <option value="">Select Vendor/Client</option>
                                        </select>
                                    </div>

                                    <div class="mt-3 col-12 col-xl-12" id="vendor-client-data" style="display: none;">
                                        <div id="fetched-data"></div>
                                        <div id="entry_part" class="mt-4"></div>

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
        function get_List_of_client_and_vendor() {
            var type = $('#type').val();
            var label = $('#vendor-client-label');
            var container = $('#vendor-client-container');

            if (type === "") {
                // Hide the vendor/client dropdown if no type is selected
                container.hide();
                $('#vendor-client-list').html('<option value="">Select Vendor/Client</option>');
                return;
            }

            // Update the label based on the selected type
            label.text('Select ' + type);

            // Show loader using SweetAlert
            Swal.fire({
                title: 'Loading...',
                text: 'Fetching ' + type + ' list, please wait.',
                icon: 'info',
                showConfirmButton: false,
                allowOutsideClick: false,
                willOpen: () => {
                    Swal.showLoading();
                }
            });

            // Send the AJAX request to get the list based on the selected type
            $.ajax({
                url: '{{ route("entry.get_list_vendor_client") }}',
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    type: type
                },
                success: function(data) {
                    Swal.close(); // Hide the loader
                    $('#fetched-data').html("");
                    $('#entry_part').html("");
                    container.show(); // Show the vendor/client dropdown
                    $('#vendor-client-list').html('<option value="">Select ' + type + '</option>');

                    if (data.length > 0) {
                        $.each(data, function(key, value) {
                            $('#vendor-client-list').append('<option value="' + value.id + '">' + value.name + '</option>');
                        });
                    } else {
                        $('#vendor-client-list').html('<option value="">No ' + type + ' Available</option>');
                    }
                },
                error: function(error) {
                    Swal.close(); // Hide the loader
                    console.error('Error fetching client/vendor list:', error);

                    Swal.fire({
                        title: 'Error!',
                        text: 'There was an error fetching the list. Please try again.',
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });

                    container.hide();
                }
            });
        }

        // Fetch additional data when Vendor/Client is selected
        $('#vendor-client-list').change(function() {
            var selectedId = $(this).val();
            var type = $('#type').val(); // Get the selected type (Client or Vendor)

            if (selectedId !== "") {
                // Show loader using SweetAlert for the second AJAX request
                Swal.fire({
                    title: 'Loading...',
                    text: 'Fetching additional data, please wait.',
                    icon: 'info',
                    showConfirmButton: false,
                    allowOutsideClick: false,
                    willOpen: () => {
                        Swal.showLoading();
                    }
                });

                $.ajax({
                    url: '{{ route("entry.get_detail") }}',
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        id: selectedId,
                        type: type
                    },
                    success: function(data) {
                        Swal.close();

                        $('#vendor-client-data').show();
                        $('#fetched-data').html(data.html);
                        $('#entry_part').html(data.entry_part);
                    },
                    error: function(error) {
                        Swal.close();
                        console.error('Error fetching additional data:', error);

                        Swal.fire({
                            title: 'Error!',
                            text: 'There was an error fetching the additional data. Please try again.',
                            icon: 'error',
                            confirmButtonText: 'OK'
                        });

                        $('#vendor-client-data').hide();
                    }
                });
            } else {
                $('#vendor-client-data').hide();
            }
        });
    </script>
@endsection
