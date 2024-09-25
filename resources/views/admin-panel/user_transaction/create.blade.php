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

    .text-danger {
        color: #dc3545; /* Bootstrap red color for error messages */
    }
</style>

<div class="content-wrapper">
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="row">
            <div class="col-xl">
                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center pb-2 p-2 pt-3">
                        <h5 class="mb-0">User Transaction Create</h5>
                        <a href="{{ route('user_transaction.index') }}">
                            <button id="create-company-btn" class="mb-0 btn btn-primary">Back</button>
                        </a>
                    </div>
                    <div class="card-body pb-2 p-2 pt-3">
                        <form id="apply-course-form" method="POST" action="{{ route('user_transaction.store') }}">
                            @csrf
                            <div class="row">
                                <h5 class="mb-3">Company Wallet Available Balance: {{ number_format($available_amount, 2) }}</h5>
                            </div>
                            <div class="row">
                                <div class="mt-1 mb-1 col-12 col-md-6 col-lg-4 col-xl-4">
                                    <label for="user_id" class="required-label">Select User</label>
                                    <select name="user_id" id="user_id" class="form-control">
                                        <option value="">Select User</option>
                                        @foreach ($userList as $user)
                                            <option value="{{ $user['id'] }}" {{$user['id'] == old('user_id') ? "selected" : ''}}>
                                                {{ $user['name'] }} - Available Balance: {{ number_format($user['amount'], 2) }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="mt-1 mb-1 col-12 col-md-6 col-lg-4 col-xl-4">
                                    <label for="transaction_type" class="required-label">Transaction Type</label>
                                    <select name="transaction_type" id="transaction_type" class="form-control">
                                        <option value="" {{ old('transaction_type') === '' ? 'selected' : '' }}>Select Type</option>
                                        <option value="credit" {{ old('transaction_type') === 'credit' ? 'selected' : '' }}>Credit</option>
                                        <option value="debit" {{ old('transaction_type') === 'debit' ? 'selected' : '' }}>Debit</option>
                                    </select>
                                    @error('transaction_type')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mt-1 mb-1 col-12 col-md-6 col-lg-4 col-xl-4">
                                    <label class="form-label required-label" for="amount">Transaction Amount</label>
                                    <input type="text" name="amount" id="amount" class="form-control" placeholder="Enter Amount" value="{{ old('amount') }}">
                                    @error('amount')
                                        <div class="text-danger mt-1">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mt-2 mb-1 col-12">
                                    <button type="submit" class="btn btn-primary mt-1">Save Transaction</button>
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
    document.addEventListener('DOMContentLoaded', function() {
        const amountInput = document.getElementById('amount');

        amountInput.addEventListener('input', function(e) {
            let value = e.target.value;
            value = value.replace(/[^0-9.]/g, ''); // Remove non-numeric characters except decimal point

            // Ensure only one decimal point is allowed
            const parts = value.split('.');
            if (parts.length > 2) {
                value = parts[0] + '.' + parts.slice(1).join('');
            }

            e.target.value = value;
        });
    });
</script>
@endsection
