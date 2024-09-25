<style>
    #detail_entry_part {
        background: aliceblue;
        padding: 10px;
        border-radius: 10px;
    }
</style>

<div class="col">
    <label class="form-label required-label" for="amount">Transaction Amount</label>
    <input type="text" class="form-control" name="amount" value="" id="amount">
</div>

<div class="col">
    <label class="form-label" for="amount">&nbsp;</label><br>
    <p class="btn btn-primary" onclick="SubmitTransaction()">Submit</p>
</div>

<script>
    function SubmitTransaction() {
        var transaction_type = $('#transaction_type').val();
        var amount = $('#amount').val();
        var formData = new FormData();
        formData.append('type', '{{ @$data['type'] }}');
        formData.append('vendor_id', '{{ @$data['id'] }}');
        formData.append('transaction_type', transaction_type);
        formData.append('amount', amount);
        var datetime = $('#datetime').val();
        formData.append('date', datetime);
        // Show loading message while the request is being processed
        Swal.fire({
            title: 'Processing...',
            text: 'Please wait while we process your transaction.',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        $.ajax({
            url: '{{ route('entry.save_transaction') }}',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                Swal.close(); // Close the loading message

                // Handle success response
                if (response.status === 'success') {
                    Swal.fire({
                        title: 'Success!',
                        text: response.message,
                        icon: 'success',
                        confirmButtonText: 'OK'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.href = `/entry/${response.bill_id}/view`;
                        }
                    });
                } else {
                    // Handle failed validation
                    Swal.fire({
                        title: 'Failed!',
                        text: response.message,
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                }
            },
            error: function(xhr) {
                Swal.close(); // Close the loading message

                // Handle server errors
                Swal.fire({
                    title: 'Error!',
                    text: xhr.responseJSON ? xhr.responseJSON.message :
                        'An unknown error occurred. Please contact the developer.',
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
            }
        });
    }
</script>
