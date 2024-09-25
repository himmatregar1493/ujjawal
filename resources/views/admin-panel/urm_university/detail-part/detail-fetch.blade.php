<style>
    .nav-item .nav-link.active {
        color: white;
        /* Text color for active tab */
        background-color: rgb(239, 239, 253) !important;
        /* Background color for active tab */
    }

    .nav-item {
        margin-left: 10px;
        margin-right: 10px;
    }

    .btn-secondary:active,
    .btn-secondary.active,
    .btn-secondary.show.dropdown-toggle,
    .show>.btn-secondary.dropdown-toggle {
        color: #fff !important;
        background-color: #696cff !important;
        border-color: #696cff !important;
    }
</style>
<div class="row ">
    
        @if(count($university_list) <= 0)
            <div class="card mb-4">
                <div class="card-body">
                    <center>No University Available</center>
                </div>
            </div>
        @else
            @foreach($university_list as $key => $item)
                <div class="col-lg-4 col-md-6 col-sm-12 mb-4 col-xl-4">
                    <div class="card">
                        <div class="card-body">
                            <form action="{{ route('application.detail') }}" method="POST">
                                @csrf
                                <div class="row">
                                    <div class="mt-1 mb-1 col-6 col-md-6">
                                        <img src="{{ asset('admin_assets/images/university_image/' . @$item['logo']) }}"
                                            class="img-fluid rounded" alt="University_logo">
                                    </div>

                                    <div class="mt-1 mb-1 col-6 col-md-6">
                                        <h5>{{ @$item['name'] }}</h5>
                                    </div>
                                </div>
                                <div class="row mt-2 mb-2">
                                    <div class="col-5"><b>Urm Name</b></div>
                                    <div class="col-2">:</div>
                                    <div class="col-5">{{ @$item['urm_name'] }}</div>
                                </div>
                                <div class="row mt-2 mb-2">
                                    <div class="col-5"><b>Urm Contact Number</b></div>
                                    <div class="col-2">:</div>
                                    <div class="col-5">{{ @$item['urm_contact_no'] }}</div>
                                </div>

                            </form>
                        </div>
                    </div>
                </div>
            @endforeach
        @endif
   
</div>

