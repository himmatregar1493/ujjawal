<style>
    input {
        min-width: 200px;
    }
</style>
<section class="mt-3">
    <div class="container-fluid">
        <h4 class="text-center text-success">PRODUCT DETAIL FILL OUT</h4>

        <div class="row">
            <div class="col-md-12 mt-12" style="overflow-x:auto;">
                <table class="table" style="background-color:#f5f5f5;">
                    <thead>
                        <tr>
                            <th>No.</th>
                            <th>Meal Items</th>
                            <th>Remaining Qty</th>
                            <th style="width: 31%">QUANTITY</th>
                            <th>Price</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td scope="row">1</td>
                            <td style="width:60%">
                                <select name="vegitable" id="vegitable" class="form-control">
                                    <option value="" class="vegitable custom-select" disabled selected>Select
                                        Products</option>
                                    @foreach ($productList as $product)
                                        <option value="{{ $product['id'] }}" data-price="{{ $product['price'] }}"
                                            data-remaining_quantity="{{ $product['remaining_quantity'] }}"
                                            data-product_unit="{{ $product['product_unit'] }}"
                                            data-product_id="{{ $product['id'] }}" class="vegitable custom-select">
                                            {{ $product['name'] }}
                                        </option>
                                    @endforeach
                                </select>
                            </td>
                            <td>
                                <p id="remaining_qty"></p>
                            </td>
                            <td style="width:1%">
                                <input type="number" id="qty" min="0" value="0" class="form-control">
                            </td>
                            <td>
                                <input type="number" step="0.001" id="price" class="form-control">
                            </td>
                            <td>
                                <p id="add" class="btn btn-primary">Add</p>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <div role="alert" id="errorMsg" class="mt-5">
                </div>
            </div>

            <form id="product_detail_form">
                <div class="col-md-12 mt-12" style="background-color:#f5f5f5;">
                    <div class="p-4">
                        <div class="text-center">
                            <h4>PRODUCT DETAILS</h4>
                        </div>

                        <div class="row" style="overflow-x:auto;">
                            <table id="receipt_bill" class="table">
                                <thead>
                                    <tr>
                                        <th rowspan="2">No.</th>
                                        <th rowspan="2">Product Name</th>
                                        <th rowspan="2" class="text-center">Price</th>
                                        <th rowspan="2">Quantity</th>
                                        <th rowspan="2">Discount</th>
                                        <th rowspan="2">Total Price</th>
                                        <th rowspan="2">Total Tax Pay</th>
                                        <th colspan="2">CGST</th>
                                        <th colspan="2">SGST</th>
                                        <th colspan="2">IGST</th>
                                        <th rowspan="2" class="text-center">Total</th>
                                        <th rowspan="2" class="text-center"></th>
                                    </tr>
                                    <tr>
                                        <th>Rate</th>
                                        <th>Amt</th>
                                        <th>Rate</th>
                                        <th>Amt</th>
                                        <th>Rate</th>
                                        <th>Amt</th>
                                    </tr>
                                </thead>
                                <tbody id="new">
                                    <!-- New rows will be dynamically added here -->
                                </tbody>
                                <tr>
                                    <td colspan="12" class="text-right text-dark">
                                    </td>
                                    <td colspan="2" class="text-right text-dark">
                                        <h6><strong>Total: ₹</strong></h6>
                                        <h6><strong>Discount: ₹</strong></h6>
                                        <h6><strong>Sub Total less discount: ₹</strong></h6>
                                        <h6><strong>Total Tax Pay: ₹</strong></h6>
                                        <h6><strong>CGST: ₹</strong></h6>
                                        <h6><strong>SGST: ₹</strong></h6>
                                        <h6><strong>IGST: ₹</strong></h6>
                                        <h6><strong>Sub Total: ₹</strong></h6>
                                    </td>
                                    <td class="text-center text-dark" colspan="1" style="text-align: right;">
                                        <h6><strong><span id="total_Pay">0</span></strong></h6>
                                        <h6><strong><span id="Discount_free">0</span></strong></h6>
                                        <h6><strong><span id="less_discount_amount">0</span></strong></h6>
                                        <h6><strong><span id="total_tax">0</span></strong></h6>
                                        <h6><strong><span id="total_cgst">0</span></strong></h6>
                                        <h6><strong><span id="total_sgst">0</span></strong></h6>
                                        <h6><strong><span id="total_igst">0</span></strong></h6>
                                        <h6><strong><span id="subTotal">0</span></strong></h6>
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <button id="" class="btn btn-primary mt-4 mb-4 float-end">Submit</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</section>
<script>
    $(document).ready(function() {



        $('#product_detail_form').submit(function(e) {
            e.preventDefault();
            var trCount = $('#new tr').length;

            if (trCount === 0) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Please add at least one product!',
                });
            } else {
                var formData = new FormData(this);

                // Append additional data (custom parameters)
                formData.append('type', '{{ @$data['type'] }}');
                formData.append('client_id', '{{ @$data['id'] }}');
                formData.append('transaction_type', '{{ $transaction_type }}');
                var datetime = $('#datetime').val();
                formData.append('date', datetime);


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
        });



        var productCount = 1; // Initialize product count
        var remainingQuantity = 0; // Track the remaining quantity for validation



        // Handle product selection and auto-fill price/remaining quantity
        $('#vegitable').change(function() {
            var selectedOption = $(this).find('option:selected');
            var price = selectedOption.data('price');
            remainingQuantity = selectedOption.data('remaining_quantity'); // Get remaining quantity
            var product_id = selectedOption.data('product_id');
            var formData = new FormData();
            formData.append('product_id', product_id);
            $.ajax({
                url: '{{ route('entry.product_detail') }}', // Update with the correct route
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    if (response.status === 'success') {
                        var product = response.product_detail;
                        $('#price').val(product.price);
                        $('#remaining_qty').text(product.remaining_quantity);
                        $('#errorMsg').hide();
                    } else {
                        $('#errorMsg').text(response.message).show();
                    }
                },
                error: function(xhr) {
                    $('#errorMsg').text('An error occurred while fetching product details.')
                        .show();
                }
            });


            // $('#price').val(price); // Auto-fill the price
            // $('#remaining_qty').text(remainingQuantity); // Display remaining quantity
            // $('#errorMsg').hide(); // Hide any previous error messages
        });

        // Handle the "Add" button click
        $('#add').click(function() {
            var selectedProduct = $('#vegitable option:selected').text();
            var product_id = $('#vegitable').val();
            var qty = parseFloat($('#qty').val());
            var price = parseFloat($('#price').val());

            // Validate input

            var isProductInList = false;
            $('#new tr').each(function() {
                var existingProductId = $(this).find('input[name="product_id[]"]').val();
                if (existingProductId == product_id) {
                    isProductInList = true;
                    return false; // Break the loop
                }
            });

            // If product is already in the list, show an error
            if (isProductInList) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'This product is already added to the list.'
                });
                return;
            }

            if (price <= 0) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Price Is Null'
                });
                return;
            }


            var formData = new FormData();
            formData.append('product_id', product_id);

            // AJAX call to fetch product details
            $.ajax({
                url: '{{ route('entry.product_detail') }}',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    if (response.status === 'success') {
                        var product = response.product_detail;
                        var newRow = `
                        <tr>
                            <td>${productCount}</td>
                            <td > ${product.name} <input type="text" hidden name="product_name[]" value="${product.name}"> <input type="text" hidden name="product_id[]" value="${product_id}"></td>
                            <td><input type="text" name="product_price[]" class="form-control price" value="${price.toFixed(2)}" /></td>
                            <td>
                                <input type="number" name="quantity[]" class="form-control quantity" value="${qty.toFixed(2)}" data-remaining_quantity="${remainingQuantity}" onkeyup="validateQuantity(this)" />
                            </td>
                            <td><input type="number" name="discount[]" class="form-control discount" value="0" /></td>
                            <td><input type="text" name="total_price[]" class="form-control total_price" readonly value="0" /></td>
                            <td><input type="text" name="tax_pay[]" class="form-control tax_pay" readonly value="0" /></td>
                            <td><input type="number" name="cgst[]" class="form-control cgst_rate" value="${product.cgst}" /></td>
                            <td><input type="text"  class="form-control cgst_amt" readonly value="0" /></td>
                            <td><input type="number" name="sgst[]" class="form-control sgst_rate" value="${product.sgst}" /></td>
                            <td><input type="text"  class="form-control sgst_amt" readonly value="0" /></td>
                            <td><input type="number" name="igst[]" class="form-control igst_rate" value="${product.igst}" /></td>
                            <td><input type="text" class="form-control igst_amt" readonly value="0" /></td>
                            <td><input type="text" name="total[]" class="form-control total" readonly value="0" /></td>
                            <td><button class="btn btn-danger remove-row">Remove</button></td>
                        </tr>
                    `;
                        $('#new').append(newRow); // Add the new row to the table
                        productCount++;

                        // Attach event listeners for recalculation when inputs change
                        attachRowEvents();

                        calculateTotals(); // Recalculate totals
                    } else {
                        $('#errorMsg').text(response.message).show();
                    }
                },
                error: function(xhr) {
                    $('#errorMsg').text('An error occurred while fetching product details.')
                        .show();
                }
            });

            // Clear input fields
            $('#qty').val(0);
            $('#price').val(0);
        });

        // Handle row removal
        $(document).on('click', '.remove-row', function() {
            $(this).closest('tr').remove();
            calculateTotals(); // Update totals after removing a row
        });

        // Function to recalculate totals whenever inputs change
        function attachRowEvents() {
            $('#new').find('.price, .quantity, .discount, .cgst_rate, .sgst_rate, .igst_rate').on(
                'input change',
                function() {
                    calculateTotals(); // Recalculate totals when any of these inputs change
                });
        }

        window.validateQuantity = function(el) {
            var enteredQuantity = parseFloat($(el).val());
            var remainingQty = parseFloat($(el).data('remaining_quantity'));
            calculateTotals();
        };
        // Function to calculate total amounts
        function calculateTotals() {
            var totalPay = 0;
            var totalDiscount = 0;
            var totalTax = 0;
            var totalCgst = 0;
            var totalSgst = 0;
            var totalIgst = 0;

            $('#new tr').each(function() {
                var price = parseFloat($(this).find('.price').val()) || 0;
                var quantity = parseFloat($(this).find('.quantity').val()) || 0;
                var discount = parseFloat($(this).find('.discount').val()) || 0;
                var cgstRate = parseFloat($(this).find('.cgst_rate').val()) || 0;
                var sgstRate = parseFloat($(this).find('.sgst_rate').val()) || 0;
                var igstRate = parseFloat($(this).find('.igst_rate').val()) || 0;

                var totalPrice = (price * quantity) - discount;
                $(this).find('.total_price').val(totalPrice.toFixed(2));

                var cgstAmt = totalPrice * (cgstRate / 100);
                var sgstAmt = totalPrice * (sgstRate / 100);
                var igstAmt = totalPrice * (igstRate / 100);

                $(this).find('.cgst_amt').val(cgstAmt.toFixed(2));
                $(this).find('.sgst_amt').val(sgstAmt.toFixed(2));
                $(this).find('.igst_amt').val(igstAmt.toFixed(2));

                var totalTaxAmt = cgstAmt + sgstAmt + igstAmt;
                $(this).find('.tax_pay').val(totalTaxAmt.toFixed(2));

                var rowTotal = totalPrice + totalTaxAmt;
                $(this).find('.total').val(rowTotal.toFixed(2));

                // Accumulate totals
                totalPay += rowTotal;
                totalDiscount += discount;
                totalTax += totalTaxAmt;
                totalCgst += cgstAmt;
                totalSgst += sgstAmt;
                totalIgst += igstAmt;
            });

            // Update the totals in the footer
            $('#total_Pay').text(totalPay.toFixed(2));
            $('#Discount_free').text(totalDiscount.toFixed(2));
            $('#total_tax').text(totalTax.toFixed(2));
            $('#total_cgst').text(totalCgst.toFixed(2));
            $('#total_sgst').text(totalSgst.toFixed(2));
            $('#total_igst').text(totalIgst.toFixed(2));
            $('#subTotal').text(totalPay.toFixed(2));
        }

        // Attach initial event listeners
        attachRowEvents();
    });
</script>
