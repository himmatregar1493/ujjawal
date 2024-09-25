<style>
        .info-box {
            border: 1px solid #ddd;
            border-radius: 5px;
            padding: 15px;
            margin-bottom: 10px;
            background-color: #f9f9f9;
        }
        .info-box b {
            color: #007bff;
        }
        .info-label {
            font-weight: bold;
        }
        .info-value {
            color: #333;
        }
    </style>
    <div class="row">
    <div class="col-12 col-md-6">
                    <div class="info-box">
                        <div class="row">
                            <div class="col-5 info-label">
                                <b>University Name</b>
                            </div>
                            <div class="col-1 text-center">
                                :
                            </div>
                            <div class="col-6 info-value">
                                {{ @$urmList['university_name'] ?? 'Not Available' }}
                            </div>
                        </div>
                    </div>
                </div>

                <!-- URM Name -->
                <div class="col-12 col-md-6">
                    <div class="info-box">
                        <div class="row">
                            <div class="col-5 info-label">
                                <b>URM Name</b>
                            </div>
                            <div class="col-1 text-center">
                                :
                            </div>
                            <div class="col-6 info-value">
                            
                                 {{ trim(@$urmList['urm_name']) ?: 'Not Available' }}
                            </div>
                        </div>
                    </div>
                </div>

                <!-- URM Contact Number -->
                <div class="col-12 col-md-6">
                    <div class="info-box">
                        <div class="row">
                            <div class="col-5 info-label">
                                <b>URM Contact No</b>
                            </div>
                            <div class="col-1 text-center">
                                :
                            </div>
                            <div class="col-6 info-value">
                                {{ @$urmList['urm_contact_no'] ?? 'Not Available' }}
                            </div>
                        </div>
                    </div>
                </div>

 <div>