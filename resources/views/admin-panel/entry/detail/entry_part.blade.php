
    <style>
        #entry_part {
            background: aliceblue;
            padding: 10px;
            border-radius: 10px;
        }
    </style>

    <div id="entry_part" class="row row-cols-1 row-cols-md-2 row-cols-lg-3">
        <div class="col">
            <label class="form-label required-label" for="transaction_type">Select Transaction Type</label>
            <select id="transaction_type" name="transaction_type" class="form-control" onchange="getDataAccordingTransactionType()">
                <option value="">Select Type</option>
                @foreach(config('constants.transaction_types') as $key => $type)
                    <option value="{{ $key }}">{{ $type }}</option>
                @endforeach
            </select>
        </div>
        <div class="col">
            <label class="form-label required-label" for="transaction_type">Enter Date </label>
            <input type="datetime-local" id="datetime" name="datetime" class="form-control">

        </div>
    </div>



    <div id="create_transaction" class="row">

    </div>

    <script>
        function getDataAccordingTransactionType() {
            var transaction_type = $('#transaction_type').val();
            var formData = new FormData();
            formData.append('type', '{{ @$data['type'] }}');
            formData.append('id', '{{ @$data['id'] }}');
            formData.append('transaction_type', transaction_type);


            $.ajax({
                url: '{{ route("entry.get_transaction_page") }}',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(data) {
                    Swal.close(); // Hide the loader
                    $('#create_transaction').html(data.html);
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
                }
            });
        }

        $(document).ready(function() {
            $('#transaction_type').change(function() {
                getDataAccordingTransactionType();
            });
        });
    </script>
    <script>
        const datetimeInput = document.getElementById('datetime');
        const now = new Date();

        // Get the UTC time
        const utcTime = now.getTime();

        // Convert UTC time to IST (UTC+5:30)
        const istTime = utcTime + (5.5 * 60 * 60 * 1000);

        // Create a new Date object with the IST time
        const istDate = new Date(istTime);

        // Format date as 'YYYY-MM-DDTHH:MM'
        const formattedDateTime = istDate.toISOString().slice(0, 16);
        datetimeInput.value = formattedDateTime;
      </script>

