@extends('layouts.admin.app')

@section('content')
    <style>
        .bill-table,
        .bill-table th,
        .bill-table td {
            border: 2px solid #ccc;
            border-collapse: collapse;
            padding: 10px;
            text-align: left;
            font-size: 14px;
        }

        .bill-table th {
            background-color: #f7f7f7;
            font-weight: bold;
        }

        .bill-summary {
            margin-top: 20px;
            width: 100%;
            border-collapse: collapse;
            border: 1px solid #ccc;
        }

        .bill-summary td {
            padding: 10px;
            text-align: right;
            font-size: 14px;
        }

        .bill-summary tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        .bill-summary td strong {
            font-weight: bold;
        }

        .bill-header {
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
            border-bottom: 2px solid #ccc;
            padding-bottom: 10px;
        }

        .bill-header div {
            font-size: 16px;
            line-height: 1.5;
        }

        .bill-header .bill-details {
            text-align: right;
        }

        .container_bill {
            background: white;
            max-width: 100%;
            overflow: scroll;
            padding: 20px;

            margin: auto;

        }
        .last_row_td{
            text-align: right !important;
        }
        td,th,table{
            border: 0px !important;
        }
    </style>
    <div class="content-wrapper">
        <div class="container-xxl flex-grow-1 container-p-y">
            <div class="row">
                <div class="col-xl">
                    <div class="container_bill mt-4">


                        <!-- Bill Header Section -->
                        <div class="bill-header">
                            <div class="bill-info">
                                <strong>Ujjal Private Limited</strong><br>
                                Partap nagar Udaipur, Rajsathan -313001<br>
                                Phone: +91 23232324242<br>
                                Email: ujjawal.in@gmail.com<br>
                                <strong>A VENTURE BY UJJAWAL</strong>
                            </div>
                            <div class="bill-details">
                                <strong>Invoice No:</strong> {{ $bill_detail['id'] }}<br>
                                <strong>Invoice Date:</strong>
                                {{ \Carbon\Carbon::parse(@$bill_detail['date'])->format('d/m/Y') }}<br>
                                <strong>Customer Name:</strong> {{ $bill_detail['customer_name'] ?? '-' }}<br>
                                <strong>Customer Address:</strong> {{ $bill_detail['customer_address'] ?? '-' }}
                            </div>
                        </div>


                        <!-- Bill Table -->
                        <table class="bill-table" style="width: 100%;">
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
                                </tr>
                                <tr>
                                    <th>%</th>
                                    <th>Amount</th>
                                    <th>%</th>
                                    <th>Amount</th>
                                    <th>%</th>
                                    <th>Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $total = 0;
                                @endphp
                                @foreach ($bill_row_detail as $product)
                                    @php
                                        $productTotal = $product['product_qty'] * $product['product_price'];
                                        $total += $productTotal;
                                    @endphp
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $product['product_name'] }}</td>
                                        <td>{{ number_format($product['product_price'], 2) }}</td>
                                        <td>{{ $product['product_qty'] }}</td>
                                        <td>{{ number_format($product['product_discount'], 2) }}</td>
                                        <td>{{ number_format($productTotal, 2) }}</td>
                                        <td>{{ number_format($product['total_tax_pay'], 2) }}</td>
                                        <td>{{ number_format($product['ratecgst'], 2) }}%</td>
                                        <td>{{ number_format($product['cgst'], 2) }}</td>
                                        <td>{{ number_format($product['ratecgst'], 2) }}%</td>
                                        <td>{{ number_format($product['sgst'], 2) }}</td>
                                        <td>{{ number_format($product['ratesgst'], 2) }}%</td>
                                        <td>{{ number_format($product['igst'], 2) }}</td>
                                        <td>{{ number_format($productTotal, 2) }}</td>
                                    </tr>
                                @endforeach
                                <tr>
                                    <td  td colspan="13" class="last_row_td"><strong>Total:</strong></td>
                                    <td>₹{{ number_format($total, 2) }}</td>
                                </tr>
                                <tr>
                                    <td  td colspan="13" class="last_row_td"><strong>Discount:</strong></td>
                                    <td>₹0.00</td>
                                </tr>
                                <tr>
                                    <td  td colspan="13" class="last_row_td"><strong>Sub Total less discount:</strong></td>
                                    <td>₹{{ number_format($total, 2) }}</td>
                                </tr>
                                <tr>
                                    <td  td colspan="13" class="last_row_td"><strong>Total Tax Pay:</strong></td>
                                    <td>₹0.00</td>
                                </tr>
                                <tr>
                                    <td  td colspan="13" class="last_row_td"><strong>CGST:</strong></td>
                                    <td>₹0.00</td>
                                </tr>
                                <tr>
                                    <td  td colspan="13" class="last_row_td"><strong>SGST:</strong></td>
                                    <td>₹0.00</td>
                                </tr>
                                <tr>
                                    <td  td colspan="13" class="last_row_td"><strong>IGST:</strong></td>
                                    <td>₹0.00</td>
                                </tr>
                                <tr>
                                    <td  td colspan="13" class="last_row_td"><strong>Sub Total:</strong></td>
                                    <td>₹{{ number_format($total, 2) }}</td>
                                </tr>
                            </tbody>
                        </table>


                        <div style="display: flex; justify-content: space-between; margin-top: 60px;">
                            <div style="text-align: left;">
                                <strong>Authorised Signatory</strong>
                            </div>

                            <div style="text-align: right;">
                                <strong>Guest Signature</strong>
                            </div>
                        </div>


                        <a href="{{ route('download.bill', $bill_detail['id']) }}" class="btn btn-primary mt-3">Download PDF</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
