@extends('layouts.admin.app')

@section('content')
<style>
    .detail-row {
        background: #f8f9fa;
        padding: 10px;
        margin-bottom: 10px;
        border-radius: 5px;
        border: 1px solid #ddd;
    }

    .detail-label {
        font-weight: bold;
    }

    .toggle-bar {
        display: flex;
        justify-content: center;
        margin-top: 20px;
        margin-bottom: 20px;
    }

    .toggle-bar button {
        padding: 10px 20px;
        margin: 0 10px;
        background-color: #007bff;
        color: white;
        border: none;
        border-radius: 5px;
        cursor: pointer;
    }

    .toggle-bar button.active {
        background-color: #0056b3;
    }

    .line-divider {
        border-top: 2px solid #ddd;
        margin: 20px 0;
    }

    .loader {
        text-align: center;
        margin-top: 20px;
        display: none; /* Hidden initially */
    }

    .loader img {
        width: 50px;
    }
</style>

<div class="content-wrapper">
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="row">
            <div class="col-xl">
                <div class="card mb-4">
                    <div class="card-body">
                        <!-- Client Details -->
                        <div class="row">
                            <!-- Detail 1 -->
                            <div class="col-12 col-md-6 col-xl-4 mb-3">
                                <div class="detail-row">
                                    <div class="detail-label">
                                        Name:
                                    </div>
                                    <div>
                                        Himmat
                                    </div>
                                </div>
                            </div>

                            <!-- Detail 2 -->
                            <div class="col-12 col-md-6 col-xl-4 mb-3">
                                <div class="detail-row">
                                    <div class="detail-label">
                                        Age:
                                    </div>
                                    <div>
                                        30
                                    </div>
                                </div>
                            </div>

                            <!-- Detail 3 -->
                            <div class="col-12 col-md-6 col-xl-4 mb-3">
                                <div class="detail-row">
                                    <div class="detail-label">
                                        Address:
                                    </div>
                                    <div>
                                        123 Main Street
                                    </div>
                                </div>
                            </div>

                            <!-- Detail 4 -->
                            <div class="col-12 col-md-6 col-xl-4 mb-3">
                                <div class="detail-row">
                                    <div class="detail-label">
                                        Email:
                                    </div>
                                    <div>
                                        himmat@example.com
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Line Divider -->
                        <div class="line-divider"></div>

                        <!-- Toggle Bar -->
                        <div class="toggle-bar">
                            <button id="transactionBtn" class="active">Transaction List</button>
                            <button id="billingBtn">Billing List</button>
                        </div>

                        <!-- Loader -->
                        <div class="loader" id="loader">
                            <img src="loader.gif" alt="Loading...">
                        </div>

                        <!-- Data Display Area -->
                        <div id="dataDisplay"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const transactionBtn = document.getElementById('transactionBtn');
        const billingBtn = document.getElementById('billingBtn');
        const loader = document.getElementById('loader');
        const dataDisplay = document.getElementById('dataDisplay');

        // Initially load Transaction List
        loadTransactionList();

        // Transaction List Button Click
        transactionBtn.addEventListener('click', function() {
            toggleActiveButton(transactionBtn, billingBtn);
            loadTransactionList();
        });

        // Billing List Button Click
        billingBtn.addEventListener('click', function() {
            toggleActiveButton(billingBtn, transactionBtn);
            loadBillingList();
        });

        function toggleActiveButton(activeBtn, inactiveBtn) {
            activeBtn.classList.add('active');
            inactiveBtn.classList.remove('active');
        }

        function showLoader() {
            loader.style.display = 'block';
        }

        function hideLoader() {
            loader.style.display = 'none';
        }

        function loadTransactionList() {
            showLoader();
            // Simulate API call
            setTimeout(function() {
                // Simulating API response
                const data = '<h3>Transaction List Data</h3><p>Transactions details here...</p>';
                dataDisplay.innerHTML = data;
                hideLoader();
            }, 1000); // Simulated API delay
        }

        function loadBillingList() {
            showLoader();
            // Simulate API call
            setTimeout(function() {
                // Simulating API response
                const data = '<h3>Billing List Data</h3><p>Billing details here...</p>';
                dataDisplay.innerHTML = data;
                hideLoader();
            }, 1000); // Simulated API delay
        }
    });
</script>

@endsection
