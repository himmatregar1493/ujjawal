    <style>
        .chat-container {
            
            margin: 0 auto;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 5px;
            background-color: #f9f9f9;
        }
        .message {
            display: flex;
            margin-bottom: 10px;
            padding: 10px;
            border-radius: 5px;
        }
        .message.sender {
            {{-- background-color: #e1f5fe; --}}
            justify-content: flex-end;
        }
        .message.receiver {
            {{-- background-color: #f1f8e9; --}}
            justify-content: flex-strat;
        }
        .message .content {
            max-width: 70%;
            word-wrap: break-word;
            padding: 10px;
            border-radius: 5px;
            background-color: #ffffff;
        }
        .message .date {
            font-size: 0.8em;
            color: #888;
            margin-top: 5px;
        }
        .message .sender-name {
            font-weight: bold;
            margin-bottom: 5px;
        }
    </style>
        <form id="commentForm" class="needs-validation" novalidate>
        <div class=" mt-1 chat-container">
                <h5>Comments</h5>
                <div id="comment_add">
                    @foreach($comments_show as $comment)
                        <div class="message {{ Auth::user()->id == $comment['sender_id'] ? 'sender' : 'receiver' }}">
                            <div class="content">
                                <div class="sender-name">{{ $comment['sender_name'] }}</div>
                                <div>{{ $comment['comment'] }}</div>
                                <div class="date">{{ DateTimeFormate( $comment['created_at']) }}</div>
                            </div>
                        </div>
                    @endforeach
                    </div>
                <div class="d-flex row" style="background: aliceblue; border-radius: 20px;">
                <div class="mt-1 mb-1 col-12 col-sm-12 col-md-4 col-xl-4">
                    <label for="commentType" class="form-label">Comment Type:</label>
                    <select id="commentType" name="commentType" class="form-select Select2" required>
                        <option value="">Select a type</option>
                        @foreach($comments as $key => $value)
                            <option value="{{$value['id']}}"> {{$value['name']}}</option>  
                        @endforeach
                    </select>
                    <div class="invalid-feedback">
                        Please select a comment type.
                    </div>
                </div>

                <div class="mt-1 mb-1 col-12 col-sm-12 col-md-6 col-xl-6">
                    <label for="comment" class="form-label">Comment:</label>
                    <textarea id="comment" class="form-control" name="comment" rows="1" required></textarea>
                    <div class="invalid-feedback">
                        Please enter a comment.
                    </div>
                </div>

                <div class="mt-1 mb-1 col-12 col-sm-12 col-md-12 col-xl-2">
                     <label for="comment" class="form-label">&nbsp; </label><br>
                    <button type="submit" class="btn btn-primary">Submit</button>
                </div>
                </div>
            </div>
        </form>
    </div>

    <script>
    $(document).ready(function () {
        // Initialize Select2
        
        $('.Select2').select2();

        $('#commentForm').on('submit', function(event) {
            event.preventDefault();

            // Get form elements
            const $form = $(this);
            const $comment = $('#comment');
            const $commentType = $('#commentType');

            // Clear previous validation states
            $form.removeClass('was-validated');
            $comment.removeClass('is-invalid');
            $commentType.removeClass('is-invalid');

            let valid = true;

            // Validate comment
            if ($.trim($comment.val()) === '') {
                $comment.addClass('is-invalid');
                valid = false;
            }

            // Validate comment type
            if ($.trim($commentType.val()) === '') {
                $commentType.addClass('is-invalid');
                valid = false;
            }

            if (!valid) {
                $form.addClass('was-validated');
                return;
            }

            // Create an object to send to the server
            const data = {
                comment: $.trim($comment.val()),
                commentType: $commentType.val(),
                application_id: @json($application_id) // Use Blade's json_encode helper to embed PHP variable
            };

            // Send data to the server using AJAX
           Swal.fire({
                title: 'Loading...',
                text: 'Please wait while we process your request.',
                icon: 'info',
                showConfirmButton: false,
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading(); // Show the spinner
                }
            });

            $.ajax({
                url: '{{ route('application-history.comments_save') }}',
                type: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                data: data,
                success: function(response) {
                    
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: response.message || 'Comment saved successfully!',
                        confirmButtonText: 'OK'
                    });
                   fetchComments();
                },
                error: function(xhr) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: 'Something went wrong!',
                        confirmButtonText: 'OK'
                    });
                }
            }).always(function() {
                Swal.close(); // Close the loading alert
            });
        });
    });


let callCount = 0;
const maxCalls = 1000;

    function fetchComments() {
            const dateElements = $('.chat-container .message .date');
            var date = "";
            // If there are any date elements
            if (dateElements.length > 0) {
                date  = $(dateElements[dateElements.length - 1]).text().trim()
            
            }
            const data = {
                date: date,
                application_id: @json($application_id) // Use Blade's json_encode helper to embed PHP variable
            };

            $.ajax({
                url: '{{ route('application-history.fetch_comments') }}',
                type: 'POST',
                data: data, 
                success: function(response) {
                    
                    const chatContainer = $('#comment_add');
                    
                    if (response.comments.length > 0) {
                        var commentHtml = "";
                        response.comments.forEach(comment => {
                            
                            const messageClass = comment.sender_id == {{ Auth::user()->id }} ? 'sender' : 'receiver';
                            commentHtml += `
                                <div class="message ${messageClass}">
                                    <div class="content">
                                        <div class="sender-name">${comment.sender_name}</div>
                                        <div>${comment.comment}</div>
                                        <div class="date">${comment.created_at}</div>
                                    </div>
                                </div>
                            `;
                            
                            
                        });

                        chatContainer.append(commentHtml);

                        // Scroll to the bottom of the chat container
                        
                    }
                },
                error: function(xhr) {
                    console.error('Error fetching comments:', xhr);
                }
            });
    }

    const intervalId = setInterval(() => {
        if (callCount >= maxCalls) {
            clearInterval(intervalId); // Stop calling fetchComments after 1000 calls
            console.log('Stopped fetching comments after 1000 calls.');
        } else {
            fetchComments();
            callCount++;
        }
    }, 10000)

    // Fetch comments every 1 second
    
    </script>
