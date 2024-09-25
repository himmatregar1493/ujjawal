<form id="student-info-submit" method="POST">
    @csrf

    <div class="row">
    <div class="mt-1 mb-1 col-12 col-md-6 col-lg-6 col-xl-6 d-flex">
            <div class="mt-1 mb-1 col-5 col-md-5 col-lg-5 col-xl-5">
               <b> Course Name</b> 
            </div>
            <div class="mt-1 mb-1 col-1 col-md-1 col-lg-1 col-xl-1">
                :
            </div>
            <div class="mt-1 mb-1 col-6 col-md-6 col-lg-6 col-xl-6">
                {{ @$courseinfo['course_name'] }}
            </div>
        </div>
        <div class="mt-1 mb-1 col-12 col-md-6 col-lg-6 col-xl-6 d-flex">
            <div class="mt-1 mb-1 col-5 col-md-5 col-lg-5 col-xl-5">
               <b>  Intake</b> 
            </div>
            <div class="mt-1 mb-1 col-1 col-md-1 col-lg-1 col-xl-1">
                :
            </div>
            <div class="mt-1 mb-1 col-6 col-md-6 col-lg-6 col-xl-6">
                {{ @$courseinfo['intake_name'] }}
            </div>
        </div>
        <div class="mt-1 mb-1 col-12 col-md-6 col-lg-6 col-xl-6 d-flex">
            <div class="mt-1 mb-1 col-5 col-md-5 col-lg-5 col-xl-5">
               <b>  Tution Fees</b> 
            </div>
            <div class="mt-1 mb-1 col-1 col-md-1 col-lg-1 col-xl-1">
                :
            </div>
            <div class="mt-1 mb-1 col-6 col-md-6 col-lg-6 col-xl-6">
                {{ @$courseinfo['tuition_fee'] }}
            </div>
        </div>
        <div class="mt-1 mb-1 col-12 col-md-6 col-lg-6 col-xl-6 d-flex">
            <div class="mt-1 mb-1 col-5 col-md-5 col-lg-5 col-xl-5">
               <b>  Institution Name.</b> 
            </div>
            <div class="mt-1 mb-1 col-1 col-md-1 col-lg-1 col-xl-1">
                :
            </div>
            <div class="mt-1 mb-1 col-6 col-md-6 col-lg-6 col-xl-6">
                {{ @$courseinfo['institute_name'] }}
            </div>
        </div>

        <div class="mt-1 mb-1 col-12 col-md-6 col-lg-6 col-xl-6 d-flex">
            <div class="mt-1 mb-1 col-5 col-md-5 col-lg-5 col-xl-5">
               <b>  University Name  </b> 
            </div>
            <div class="mt-1 mb-1 col-1 col-md-1 col-lg-1 col-xl-1">
                :
            </div>
            <div class="mt-1 mb-1 col-6 col-md-6 col-lg-6 col-xl-6">
                {{ @$courseinfo['university_name'] }}
            </div>
        </div>

        
        
        
        
    </div>
</form>
