<style>
    @media (max-width: 700px) {
        .detail-table {
            display: none;
        }
        .detail-card {
            display: block;
        }
    }

    @media (min-width: 700px) {
        .detail-table {
            display: table;
        }
        .detail-card {
            display: none;
        }
    }

    /* Additional styling for transaction status badges */
    .badge-success {
        background-color: #28a745; /* Green for success */
        color: #fff;
    }

    .badge-danger {
        background-color: #dc3545; /* Red for failed */
        color: #fff;
    }

    .badge-warning {
        background-color: #ffc107; /* Yellow for processing */
        color: #000;
    }

    .badge-secondary {
        background-color: #6c757d; /* Grey for default */
        color: #fff;
    }

    /* Row color based on status */
    .row-success {
        background-color: #d4edda; /* Light green for success */
    }

    /* Badge styles for transaction types */
.badge-credit {
    background-color: #007bff; /* Blue for credit */
    color: #fff;
}

.badge-debit {
    background-color: #dc3545; /* Red for debit */
    color: #fff;
}

/* Row color based on status */
.row-success {
    background-color: #d4edda; /* Light green for success */
}

.row-danger {
    background-color: #f8d7da; /* Light red for failed */
}

.row-warning {
    background-color: #fff3cd; /* Light yellow for processing */
}

.row-secondary {
    background-color: #e2e3e5; /* Light grey for default */
}


    .row-danger {
        background-color: #f8d7da; /* Light red for failed */
    }

    .row-warning {
        background-color: #fff3cd; /* Light yellow for processing */
    }

    .row-secondary {
        background-color: #e2e3e5; /* Light grey for default */
    }
    th,td{
        min-width: 200px;
    }
</style>


@if (count($transactions) <= 0)
<div class="card-body d-flow text-center">
    No record found
</div>
@else
<div class="detail-table">
    <table class="table">
        <thead>
            <tr>
                <th>Transaction ID</th>
                <th>Name</th>
                <th>Amount</th>
                <th>Transaction Type</th>
                <th>Transaction Status</th>
                <th>Sender</th>
                <th>Receiver</th>
                <th>Transaction Done By</th>
                <th>Remark</th>
                <th>Created At</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($transactions as $item)
                @php
                    $statusClass = '';
                    $rowClass = '';
                    $typeClass = '';

                    switch ($item['transaction_status']) {
                        case 'success':
                            $statusClass = 'badge-success';
                            $rowClass = 'row-success';
                            break;
                        case 'failed':
                            $statusClass = 'badge-danger';
                            $rowClass = 'row-danger';
                            break;
                        case 'processing':
                            $statusClass = 'badge-warning';
                            $rowClass = 'row-warning';
                            break;
                        default:
                            $statusClass = 'badge-secondary';
                            $rowClass = 'row-secondary';
                    }

                    switch ($item['transaction_type']) {
                        case 'credit':
                            $typeClass = 'badge-credit';
                            break;
                        case 'debit':
                            $typeClass = 'badge-debit';
                            break;
                        default:
                            $typeClass = 'badge-secondary';
                    }
                @endphp
                <tr class="{{ $rowClass }}">
                    <td>{{ @$item['id'] }}</td>
                    <td>{{ WalletIdToUserDetailGet(@$item['wallet_id']) }}</td>
                    <td>{{ @$item['transaction_amount'] }}</td>
                    <td><span class="badge {{ $typeClass }}">{{ @$item['transaction_type'] }}</span></td>
                    <td><span class="badge {{ $statusClass }}">{{ @$item['transaction_status'] }}</span></td>
                    <td>{{ @$item['sender_name'] }}</td>
                    <td>{{ @$item['receiver_name'] }}</td>
                    <td>{{ @$item['created_by'] }}</td>
                    <td>{{ @$item['remark'] }}</td>
                    <td>{{ DateTimeFormate($item['created_at']) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

<div class="detail-card">
    @foreach ($transactions as $item)
        @php
            // Use same logic to determine badge classes
            $statusClass = '';
            $typeClass = '';

            switch ($item['transaction_status']) {
                case 'success':
                    $statusClass = 'badge-success';
                    break;
                case 'failed':
                    $statusClass = 'badge-danger';
                    break;
                case 'processing':
                    $statusClass = 'badge-warning';
                    break;
                default:
                    $statusClass = 'badge-secondary';
            }

            switch ($item['transaction_type']) {
                case 'credit':
                    $typeClass = 'badge-credit';
                    break;
                case 'debit':
                    $typeClass = 'badge-debit';
                    break;
                default:
                    $typeClass = 'badge-secondary';
            }
        @endphp
        <div class="row pt-3 pb-3 mt-1 mb-1" style="border: 1px solid black; border-radius: 10px; margin-bottom: 10px;">
            <div class="col-12 d-flex">
                <div class="col-6">
                    ID:
                </div>
                <div class="col-6">
                    {{ @$item['id'] }}
                </div>
            </div>
            <div class="col-12 d-flex">
                <div class="col-6">
                    Amount:
                </div>
                <div class="col-6">
                    {{ @$item['transaction_amount'] }}
                </div>
            </div>
            <div class="col-12 d-flex">
                <div class="col-6">
                    Transaction Type:
                </div>
                <div class="col-6">
                    <span class="badge {{ $typeClass }}">{{ @$item['transaction_type'] }}</span>
                </div>
            </div>
            <div class="col-12 d-flex">
                <div class="col-6">
                    Status:
                </div>
                <div class="col-6">
                    <span class="badge {{ $statusClass }}">{{ @$item['transaction_status'] }}</span>
                </div>
            </div>
            <div class="col-12 d-flex">
                <div class="col-6">
                    Transaction Done By:
                </div>
                <div class="col-6">
                    {{ @$item['created_by'] }}
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
        </div>
    @endforeach
</div>
@endif
