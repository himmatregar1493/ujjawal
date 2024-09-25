<form id="student-info-submit" method="POST" >
    @csrf
    
    <div class="row">
        <div class="mt-1 mb-1 col-12 col-md-6 col-lg-4 col-xl-4">
            <label class="form-label required-label" for="student_passport">Student Passport No.</label>
            <input type="text" name="student_passport" id="student_passport" maxlengh="20" class="form-control" value="{{@$student_info['student_passport']}}" required>
            <div class="invalid-feedback">Student Passport No. is required.</div>
        </div>
        <div class="mt-1 mb-1 col-12 col-md-6 col-lg-4 col-xl-4">
            <label class="form-label required-label" for="student_first_name">Student First Name</label>
            <input type="text" name="student_first_name" id="student_first_name" maxlengh="50" value="{{@$student_info['student_first_name']}}" class="form-control" required>
            <div class="invalid-feedback">Student First Name is required.</div>
        </div>
        <div class="mt-1 mb-1 col-12 col-md-6 col-lg-4 col-xl-4">
            <label class="form-label required-label" for="student_last_name">Student Last Name</label>
            <input type="text" name="student_last_name" id="student_last_name" maxlengh="50" value="{{@$student_info['student_last_name']}}" class="form-control" required>
            <div class="invalid-feedback">Student Last Name is required.</div>
        </div>
        <div class="mt-1 mb-1 col-12 col-md-6 col-lg-4 col-xl-4">
            <label class="form-label required-label" for="student_email">Enter Student E-Mail ID</label>
            <input type="email" name="student_email" id="student_email" class="form-control" value="{{@$student_info['student_email']}}" placeholder="Enter Student E-Mail ID" required>
            <div class="invalid-feedback">Enter Student E-Mail ID is required.</div>
        </div>
        <div class="mt-1 mb-1 col-12 col-md-6 col-lg-4 col-xl-4">
            <label class="form-label required-label" for="counsellor_email">Email-id of counsellor for communication regarding this student (India Only)</label>
            <input type="email" name="counsellor_email" id="counsellor_email" value="{{@$student_info['counsellor_email']}}" class="form-control" placeholder="Email-id of counsellor for communication regarding this student (India Only)" required>
            <div class="invalid-feedback">Email-id of counsellor for communication regarding this student is required.</div>
        </div>
      
<div class="mt-1 mb-1 col-12 col-md-6 col-lg-4 col-xl-4">
    <label class="form-label required-label" for="student_whatsapp">Student Whatsapp Number</label>
    <input type="text" name="student_whatsapp" id="student_whatsapp"  maxlengh="15"
           value="{{ @$student_info['student_whatsapp'] }}" 
           class="form-control" 
           placeholder="Student Whatsapp Number" 
           required>
    <div class="invalid-feedback">Student Whatsapp Number is required and must be a valid number.</div>
</div>

<div class="mt-1 mb-1 col-12 col-md-6 col-lg-4 col-xl-4">
    <label class="form-label required-label" for="counsellor_number">Counsellor Number</label>
    <input type="text" name="counsellor_number" id="counsellor_number" 
           value="{{ @$student_info['counsellor_number'] }}" maxlengh="15"
           class="form-control" 
           placeholder="Counsellor Number" 
           required>
    <div class="invalid-feedback">Counsellor Number is required and must be a valid number.</div>
</div>
        <div class="mt-1 mb-1 col-12 col-md-6 col-lg-4 col-xl-4">
            <label class="form-label required-label" for="gender">Gender</label>
            <select class="form-control" id="gender" name="gender" required>
                <option value="">Select Gender</option>
                <option value="Male" {{@$student_info['gender'] == "Male" ? "selected" : ""}}>Male</option>
                <option value="Female" {{@$student_info['gender'] == "Female" ? "selected" : ""}}>Female</option>
            </select>
            <div class="invalid-feedback">Gender is required.</div>
        </div>
        <div class="mt-1 mb-1 col-12 col-md-6 col-lg-4 col-xl-4">
            <label class="form-label required-label" for="visa_refusal">Any Previous Visa Refusal</label>
            <select class="form-control" id="visa_refusal" name="visa_refusal" required>
                <option value="">Select</option>
                <option value="Yes" {{@$student_info['visa_refusal'] == "Yes" ? "selected" : ""}}>Yes</option>
                <option value="No" {{@$student_info['visa_refusal'] == "No" ? "selected" : ""}}>No</option>
            </select>
            <div class="invalid-feedback">Any Previous Visa Refusal is required.</div>
        </div>

        <!-- Reason Input Field -->
        <div class="mt-1 mb-1 col-12 col-md-6 col-lg-4 col-xl-4" id="visa_reason_div" @if(@$student_info['visa_refusal'] == "Yes") @else  style="display: none;" @endif>
            <label class="form-label" for="visa_reason">Reason for Visa Refusal</label>
            <input type="text" class="form-control" id="visa_reason" name="visa_reason" value="{{ @$student_info['visa_refusal_reason'] }}" maxlengh="1000" placeholder="Enter reason">
        </div>
        <div class="mt-2 mb-1 col-12 col-md-12 col-lg-12 col-xl-12">
        <input type="text" value="{{$application_id}}" name="application_id" hidden>
        <input type="text" value="{{@$student_info['id']}}" name="id" hidden>
        
            <button type="button" onclick="validateForm()" class="btn btn-primary mt-1">Save Information</button>
        </div>
    </div>
</form>
<script>
$(document).ready(function() {
    const $visaRefusalSelect = $('#visa_refusal');
    const $visaReasonDiv = $('#visa_reason_div');

    // Function to toggle reason input field visibility
    function toggleVisaReason() {
        if ($visaRefusalSelect.val() === 'Yes') {
            $visaReasonDiv.show();
        } else {
            $visaReasonDiv.hide();
        }
    }

    // Initial check in case of pre-selected value
    toggleVisaReason();

    // Add event listener for changes in the select dropdown
    $visaRefusalSelect.on('change', toggleVisaReason);
});
</script>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('student-info-submit');

    form.addEventListener('input', (event) => {
        const input = event.target;
        if (input.tagName.toLowerCase() === 'input' || input.tagName.toLowerCase() === 'select') {
            const errorDiv = input.nextElementSibling;
            if (input.value.trim()) {
                input.classList.remove('is-invalid');
                errorDiv.classList.remove('d-block');
            }
        }
    });

    form.addEventListener('submit', (event) => {
        event.preventDefault(); // Prevent the default form submission
        validateForm();
    });
});

function validateForm(event) {
     // Prevent form submission for validation

    const form = document.getElementById('student-info-submit');
    const inputs = form.querySelectorAll('input[required], select[required]');
    let isValid = true;

    inputs.forEach((input) => {
        const errorDiv = input.nextElementSibling;
        if (!input.value.trim()) {
            errorDiv.innerHTML = `${input.previousElementSibling.innerText} is required.`;
            input.classList.add('is-invalid');
            errorDiv.classList.add('d-block');
            isValid = false;
        } else if (input.type === 'email' && !validateEmail(input.value)) {
            errorDiv.innerHTML = 'Please enter a valid email address.';
            input.classList.add('is-invalid');
            errorDiv.classList.add('d-block');
            isValid = false;
        } else {
            input.classList.remove('is-invalid');
            errorDiv.classList.remove('d-block');
        }
    });

    if (isValid) {
        submitForm();
    }
}


function validateEmail(email) {
    // Simple email validation regex
    const re = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
    return re.test(email);
}

function submitForm() {
    const form = document.getElementById('student-info-submit');
    
    Swal.fire({
        title: 'Loading...',
        html: 'Please wait while we process your request.',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });

    $.ajax({
        url: '{{ Route('application.student_info_save') }}', // Use the action attribute from the form
        type: 'POST',
        data: $(form).serialize(),
        success: function(response) {
            Swal.close();
            Swal.fire({
                icon: 'success',
                title: 'Success!',
                text: 'Data has been saved successfully.',
                confirmButtonText: 'OK'
            });
            GetStudentInfo(); // Assuming this function refreshes the student information on the page
        },
        error: function(xhr) {
            Swal.close();
            const errors = xhr.responseJSON.errors;
            if (errors) {
                // Clear previous errors
                const inputs = form.querySelectorAll('input, select');
                inputs.forEach(input => {
                    const errorDiv = input.nextElementSibling;
                    input.classList.remove('is-invalid');
                    errorDiv.classList.remove('d-block');
                });

                // Display new errors
                for (const [field, messages] of Object.entries(errors)) {
                    const input = form.querySelector(`[name=${field}]`);
                    if (input) {
                        const errorDiv = input.nextElementSibling;
                        input.classList.add('is-invalid');
                        errorDiv.innerHTML = messages.join('<br>');
                        errorDiv.classList.add('d-block');
                    }
                }
            }
           
        }
    });
}
</script>


<script>
$(document).ready(function() {
     $('#student_whatsapp, #counsellor_number').on('input', function() {
        // Get the current value of the input
        let value = this.value;
        
        // If the value starts with a '+', allow it, otherwise remove it
        if (value.startsWith('+')) {
            value = '+' + value.slice(1).replace(/[^0-9]/g, '');
        } else {
            value = value.replace(/[^0-9]/g, '');
        }
        
        // Update the input value
        this.value = value;
    });

});
</script>

