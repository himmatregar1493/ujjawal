<form id="student-info-submit" method="POST" >
    @csrf
    
    <div class="row">
        <div class="mt-1 mb-1 col-12 col-md-6 col-lg-6 col-xl-6 d-flex">
    <div class="mt-1 mb-1 col-5 col-md-5 col-lg-5 col-xl-5">
       <b> Student Passport No. </b> 
    </div>
    <div class="mt-1 mb-1 col-1 col-md-1 col-lg-1 col-xl-1">
        :
    </div>
    <div class="mt-1 mb-1 col-6 col-md-6 col-lg-6 col-xl-6">
        {{ @$student_info['student_passport'] }}
    </div>
</div>

<div class="mt-1 mb-1 col-12 col-md-6 col-lg-6 col-xl-6 d-flex">
    <div class="mt-1 mb-1 col-5 col-md-5 col-lg-5 col-xl-5">
       <b> Student First Name </b> 
    </div>
    <div class="mt-1 mb-1 col-1 col-md-1 col-lg-1 col-xl-1">
        :
    </div>
    <div class="mt-1 mb-1 col-6 col-md-6 col-lg-6 col-xl-6">
        {{ @$student_info['student_first_name'] }}
    </div>
</div>

<div class="mt-1 mb-1 col-12 col-md-6 col-lg-6 col-xl-6 d-flex">
    <div class="mt-1 mb-1 col-5 col-md-5 col-lg-5 col-xl-5">
       <b> Student Last Name </b> 
    </div>
    <div class="mt-1 mb-1 col-1 col-md-1 col-lg-1 col-xl-1">
        :
    </div>
    <div class="mt-1 mb-1 col-6 col-md-6 col-lg-6 col-xl-6">
        {{ @$student_info['student_last_name'] }}
    </div>
</div>

<div class="mt-1 mb-1 col-12 col-md-6 col-lg-6 col-xl-6 d-flex">
    <div class="mt-1 mb-1 col-5 col-md-5 col-lg-5 col-xl-5">
       <b> Enter Student E-Mail ID </b> 
    </div>
    <div class="mt-1 mb-1 col-1 col-md-1 col-lg-1 col-xl-1">
        :
    </div>
    <div class="mt-1 mb-1 col-6 col-md-6 col-lg-6 col-xl-6">
        {{ @$student_info['student_email'] }}
    </div>
</div>



<div class="mt-1 mb-1 col-12 col-md-6 col-lg-6 col-xl-6 d-flex">
    <div class="mt-1 mb-1 col-5 col-md-5 col-lg-5 col-xl-5">
      <b>  Student Whatsapp Number </b> 
    </div>
    <div class="mt-1 mb-1 col-1 col-md-1 col-lg-1 col-xl-1">
        :
    </div>
    <div class="mt-1 mb-1 col-6 col-md-6 col-lg-6 col-xl-6">
        {{ @$student_info['student_whatsapp'] }}
    </div>
</div>



<div class="mt-1 mb-1 col-12 col-md-6 col-lg-6 col-xl-6 d-flex">
    <div class="mt-1 mb-1 col-5 col-md-5 col-lg-5 col-xl-5">
       <b> Gender </b> 
    </div>
    <div class="mt-1 mb-1 col-1 col-md-1 col-lg-1 col-xl-1">
        :
    </div>
    <div class="mt-1 mb-1 col-6 col-md-6 col-lg-6 col-xl-6">
        {{ @$student_info['gender'] }}
    </div>
</div>

<div class="mt-1 mb-1 col-12 col-md-6 col-lg-6 col-xl-6 d-flex">
    <div class="mt-1 mb-1 col-5 col-md-5 col-lg-5 col-xl-5">
       <b> Any Previous Visa Refusal </b> 
    </div>
    <div class="mt-1 mb-1 col-1 col-md-1 col-lg-1 col-xl-1">
        :
    </div>
    <div class="mt-1 mb-1 col-6 col-md-6 col-lg-6 col-xl-6">
        {{ @$student_info['visa_refusal'] }}
    </div>
</div>
    @if($student_info['visa_refusal_reason'])
    <div class="mt-1 mb-1 col-12 col-md-6 col-lg-6 col-xl-6 d-flex" id="visa_reason_div" @if(@$student_info['visa_refusal'] == "Yes") @else style="display: none;" @endif>
        <div class="mt-1 mb-1 col-5 col-md-5 col-lg-5 col-xl-5">
          <b>  Reason for Visa Refusal </b> 
        </div>
        <div class="mt-1 mb-1 col-1 col-md-1 col-lg-1 col-xl-1">
            :
        </div>
        <div class="mt-1 mb-1 col-6 col-md-6 col-lg-6 col-xl-6">
            {{ @$student_info['visa_refusal_reason'] }}
        </div>
    </div>
    @endif

<div class="mt-1 mb-1 col-12 col-md-6 col-lg-6 col-xl-6 d-flex">
    <div class="mt-1 mb-1 col-5 col-md-5 col-lg-5 col-xl-5">
      <b>  Counsellor Number </b> 
    </div>
    <div class="mt-1 mb-1 col-1 col-md-1 col-lg-1 col-xl-1">
        :
    </div>
    <div class="mt-1 mb-1 col-6 col-md-6 col-lg-6 col-xl-6">
        <span id="counsellor_number_text">{{ @$student_info['counsellor_number'] }}</span>
        <input type="text" id="counsellor_number_input" name="counsellor_number" value="{{ @$student_info['counsellor_number'] }}" class="form-control d-none">
        <div class="invalid-feedback"></div> <!-- Error message container -->
        <button type="button" onclick="toggleEdit('counsellor_number')" class="btn btn-secondary btn-sm">Edit</button>
    </div>
</div>

<!-- Email-id of counsellor -->
<div class="mt-1 mb-1 col-12 col-md-6 col-lg-6 col-xl-6 d-flex">
    <div class="mt-1 mb-1 col-5 col-md-5 col-lg-5 col-xl-5">
      <b>  Email-id of counsellor for communication regarding this student (India Only) </b> 
    </div>
    <div class="mt-1 mb-1 col-1 col-md-1 col-lg-1 col-xl-1">
        :
    </div>
    <div class="mt-1 mb-1 col-6 col-md-6 col-lg-6 col-xl-6">
        <span id="counsellor_email_text">{{ @$student_info['counsellor_email'] }}</span>
        <input type="text" id="counsellor_email_input" name="counsellor_email" value="{{ @$student_info['counsellor_email'] }}" class="form-control d-none">
        <div class="invalid-feedback"></div> <!-- Error message container -->
        <button type="button" onclick="toggleEdit('counsellor_email')" class="btn btn-secondary btn-sm">Edit</button>
    </div>
</div>
        <!-- Reason Input Field -->
        
        
    </div>
</form>
<script>
function toggleEdit(field) {
    const textElem = document.getElementById(`${field}_text`);
    const inputElem = document.getElementById(`${field}_input`);
    const button = document.querySelector(`#${field}_text ~ button`);

    if (inputElem.classList.contains('d-none')) {
        // Switch to edit mode
        inputElem.classList.remove('d-none');
        textElem.classList.add('d-none');
        button.textContent = 'Save'; 
    } else {
        // Switch to view mode
        inputElem.classList.add('d-none');
        textElem.classList.remove('d-none');
        textElem.textContent = inputElem.value; 

        // Create FormData object based on the field being edited
        let share_data = new FormData();
        share_data.append(field, inputElem.value);
        share_data.append('application_id', {{$application_id}});
        share_data.append('id', {{$student_info['id']}}); // Append the field and its new value
        
        studentInfoSave(share_data, field);

        button.textContent = 'Edit'; 
    }
}

function studentInfoSave(share_data, field) {
    Swal.fire({
        title: 'Loading...',
        html: 'Please wait while we process your request.',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });

    $.ajax({
        url: '{{ route('application-history.student_info_save') }}',
        type: 'POST',
        data: share_data,
        processData: false,
        contentType: false,
        success: function(response) {
            Swal.close();
            Swal.fire({
                icon: 'success',
                title: 'Success!',
                text: 'Data has been saved successfully.',
                confirmButtonText: 'OK'
            });
            studentDetail(); // Refreshes the student information on the page
        },
        error: function(xhr) {
            Swal.close();
            const errors = xhr.responseJSON.errors;
            console.log(errors);

            // Clear previous errors
            document.querySelectorAll('.form-control').forEach(input => {
                input.classList.remove('is-invalid');
                const errorDiv = input.nextElementSibling;
                if (errorDiv) errorDiv.classList.remove('d-block');
            });

            // Display new errors
            if (errors) {
                for (const [field, messages] of Object.entries(errors)) {
                    const input = document.querySelector(`[name=${field}]`);
                    if (input) {
                        const errorDiv = input.nextElementSibling;
                        input.classList.add('is-invalid');
                        if (errorDiv) {
                            errorDiv.innerHTML = messages.join('<br>');
                            errorDiv.classList.add('d-block');
                        }
                    }
                }
            }
        }
    });
}
</script>


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

