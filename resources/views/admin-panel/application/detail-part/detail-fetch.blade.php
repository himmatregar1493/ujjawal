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
<div class="row">
    <div class="col-xl">
    @if(count($courses) <=  0)
    <div class="card mb-4">
        <div class="card-body">
            <center>
            No Course Found 
            </center>
        </div>
        </div>
    @else
        @foreach($courses as $key => $course)
        
            <div class="card mb-4">
                <div class="card-body">
                    <form action="{{ route('application.detail') }}" method="POST">
                        @csrf
                        <div class="row">
                            <div class="mt-1 mb-1 col-12 col-md-6 col-lg-4 col-xl-4">
                                <img src="{{ asset('admin_assets\images\course_image') }}\{{@$course['cover_image']}}"
                                    class="img-fluid rounded" alt="Responsive image">
                            </div>
                            <div class="mt-1 mb-1 col-12 col-md-6 col-lg-8 col-xl-8">
                                <h3>{{@$course['university_name']}}</h3>
                                <div class="row col-12 col-md-12 col-lg-12 col-xl-12 mt-2 mb-2">
                                    <div class="col-5 col-sm-5 col-md-5 col-lg-5">
                                        <b>Course Name</b>
                                    </div>
                                    <div class="col-2 col-sm-2 col-md-2 col-lg-2">:</div>
                                    <div class="col-5 col-sm-5 col-md-5 col-lg-5">{{@$course['course_name']}}</div>
                                </div>
                                <div class="row col-12 col-md-12 col-lg-12 col-xl-12 mt-2 mb-2">
                                    <div class="col-5 col-sm-5 col-md-5 col-lg-5">
                                        <b>Campus</b>
                                    </div>
                                    <div class="col-2 col-sm-2 col-md-2 col-lg-2">:</div>
                                    <div class="col-5 col-sm-5 col-md-5 col-lg-5">{{@$course['campus']}}</div>
                                </div>
                                <div class="row col-12 col-md-12 col-lg-12 col-xl-12 mt-2 mb-2">
                                    <div class="col-5 col-sm-5 col-md-5 col-lg-5">
                                        <b>Tution Fees</b>
                                    </div>
                                    <div class="col-2 col-sm-2 col-md-2 col-lg-2">:</div>
                                    <div class="col-5 col-sm-5 col-md-5 col-lg-5">{{@$course['tuition_fees_inr']}} </div>
                                </div>
                                <div class="row col-12 col-md-12 col-lg-12 col-xl-12 mt-2 mb-2">
                                    <div class="col-5 col-sm-5 col-md-5 col-lg-5">
                                        <b>Duration</b>
                                    </div>
                                    <div class="col-2 col-sm-2 col-md-2 col-lg-2">:</div>
                                    <div class="col-5 col-sm-5 col-md-5 col-lg-5">{{@$course['duration']}}</div>
                                </div>
                                <div class="row col-12 col-md-12 col-lg-12 col-xl-12 mt-2 mb-2">
                                    <div class="col-5 col-sm-5 col-md-5 col-lg-5">
                                        <b>Course Type</b>
                                    </div>
                                    <div class="col-2 col-sm-2 col-md-2 col-lg-2">:</div>
                                    <div class="col-5 col-sm-5 col-md-5 col-lg-5">{{ ucfirst(@$course['course_type']) }}</div>
                                </div>
                                <div class="row col-12 col-md-12 col-lg-12 col-xl-12 mt-2 mb-2">
                                    <div class="col-5 col-sm-5 col-md-5 col-lg-5">
                                        <b>Location</b>
                                    </div>
                                    <div class="col-2 col-sm-2 col-md-2 col-lg-2">:</div>
                                    <div class="col-5 col-sm-5 col-md-5 col-lg-5">{{@$course['location']}}</div>
                                </div>
                                <div class="col-12 col-md-12 col-lg-12 col-xl-12 mt-2 mb-2">
                                <button type="submit" class="btn btn-primary mt-1 w-auto ml-2">Process Continue</button>
                                <input type="text" name="course_id" value="{{@$course['id']}}" hidden>
                                <input type="text" name="intake_id" value="{{@$intake_id}}" hidden>
                            </div>
                            </div>
                        </div>
            
                        
                    </form>

                    <hr>
                    <div class="mt-3 ">
                        <div class="col-12 col-md-12 col-lg-12 col-xl-12 mt-2 mb-2">
                            <ul class="nav nav-tabs" id="myTab" role="tablist">
                                <li class="nav-item " role="presentation">
                                    <a class="btn btn-secondary active mt-1 w-auto ml-2" id="london-tab"
                                        data-bs-toggle="tab" href="#london" role="tab" aria-controls="london"
                                        aria-selected="true">General Entry
                                        Requirement</a>
                                </li>
                                <li class="nav-item  " role="presentation">
                                    <a class="btn btn-secondary mt-1 w-auto ml-2" id="paris-tab" data-bs-toggle="tab"
                                        href="#paris" role="tab" aria-controls="paris" aria-selected="false">Entry Requirement</a>
                                </li>
                                {{-- <li class="nav-item  " role="presentation">
                                    <a class="btn btn-secondary mt-1 w-auto ml-2" id="tokyo-tab" data-bs-toggle="tab"
                                        href="#tokyo" role="tab" aria-controls="tokyo" aria-selected="false">Time
                                        Line</a>
                                </li> --}}
                            </ul>
                            <div class="tab-content" id="myTabContent">
                                <div class="tab-pane fade show active" id="london" role="tabpanel" aria-labelledby="london-tab">
                                    {{@$course['general_requirement']}}
                                </div>
                                <div class="tab-pane fade" id="paris" role="tabpanel" aria-labelledby="paris-tab">
                                       {{@$course['entry_requirement']}}
                                </div>
                                {{-- <div class="tab-pane fade" id="tokyo" role="tabpanel" aria-labelledby="tokyo-tab">
                                    <h3>Tokyo</h3>
                                    <p>Tokyo is the capital of Japan.</p>
                                </div> --}}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    @endif
        
        
    </div>

</div>


<script>
    $(document).ready(function() {
        $('#apply-course-form').on('submit', function(e) {
            e.preventDefault();
            $('#detail-page').html(`
            
                <div class="card w-100">
                     @include('admin-panel.components.loader')
                </div> 
           
            `);

            $.ajax({
                url: '{{ route('application.detail-fetch') }}',
                type: 'POST',
                data: $(this).serialize(),
                success: function(response) {
                    $('#detail-page').html(response);
                },
                error: function(xhr) {
                    $('#detail-page').html("Error Occurred. Please try again.");
                }
            });
        });
    });
</script>
