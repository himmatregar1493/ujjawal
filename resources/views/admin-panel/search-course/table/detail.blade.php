<style>
    
.course-card {
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 15px;
            display: flex;
            align-items: center;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            margin: 20px 0;
        }
        .course-card img {
            width: 150px;
            height: 150px;
            border-radius: 8px;
            object-fit: cover;
            margin-right: 20px;
        }
        .course-info {
            flex-grow: 1;
        }
        .course-info h5 {
            font-weight: bold;
            color: #007bff;
        }
        .course-info p {
            margin: 0;
            color: #555;
        }
        .course-info .tags span {
            background: linear-gradient(to right, #ff7e5f, #feb47b);
            padding: 5px 10px;
            border-radius: 20px;
            margin-right: 5px;
            color: white;
            font-size: 14px;
        }
        .apply-button {
            align-self: start;
            margin: auto;
        }
        .apply-button a {
            background-color: #007bff;
            color: white;
            padding: 10px 20px;
            border-radius: 25px;
            text-decoration: none;
            font-weight: bold;
        }
        .apply-button a:hover {
            background-color: #0056b3;
        }
</style>

    
    

    <div class="detail-card" >
        @if (count($courses) <= 0) 
        
        <div class="card-body d-flow" style="text-align:center;">
            No record Found
            </div>
    
        @else
            <div class=" d-flex">
                <h4 class="mt-0 mb-0">Total Courses: {{count($courses)}}</h4>
            </div>
            @foreach ($courses as $item)
                <div class="course-card d-flex">
                    <img src="{{asset('admin_assets/images/course_image')}}/{{$item['cover_image']}}" alt="Course Image">
                    <div class="course-info">
                        <h5>{{$item['course_name']}}</h5>
                        <p>{{$item['university_name']}}</p>
                        <div class="tags mt-2">
                            @if(!empty($item['course_type']))
                                <span>{{ $item['course_type'] }}</span>
                            @endif

                            @if(!empty($item['duration']))
                                <span>{{ $item['duration'] }}</span>
                            @endif

                            @if(!empty($item['location']))
                                <span>{{ $item['location'] }}</span>
                            @endif
                        </div>

                    </div>
                     <div class="apply-button">
                        <p href="#" class="apply-now btn btn-primary" data-id="{{ $item['id'] }}">Apply Now</p>
                    </div>
                </div>
            @endforeach
        @endif
   
    
   
    <script>
    
       $('.apply-now').on('click', function(e) {
            e.preventDefault();
            
            let courseId = $(this).data('id');
            let formData = new FormData();
            
            formData.append('courseId', courseId);
            formData.append('_token', $('meta[name="csrf-token"]').attr('content')); // Add CSRF token

            $.ajax({
                url: '{{ route('search-course.get_intake_detail') }}',
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    // Generate intake selection options
                    let intakeOptions = '';
                    response.forEach(function(intake) {
                        intakeOptions += `<option value="${intake.id}">${intake.name}</option>`;
                    });

                    // Show SweetAlert with intake selection
                    Swal.fire({
                        title: 'Select Intake',
                        html: `
                            <form id="intakeForm" action="{{ route('application.detail') }}" method="POST">
                                @csrf
                                <input type="hidden" name="course_id" value="${courseId}">
                                <input type="hidden" name="type" value="course_type">
                                
                                <select name="intake_id" id="intakeSelect" class="swal2-input">
                                    ${intakeOptions}
                                </select>
                            </form>
                        `,
                        confirmButtonText: 'Submit',
                        showCancelButton: true,
                        preConfirm: () => {
                            // Return the selected intake ID
                            return document.getElementById('intakeSelect').value;
                        }
                    }).then((result) => {
                        if (result.isConfirmed) {
                            // Submit the form to the specified route
                            $('#intakeForm').submit();
                        }
                    });
                },
                error: function(xhr) {
                    console.log('Error fetching intake options:', xhr);
                }
            });
        });

    </script>
