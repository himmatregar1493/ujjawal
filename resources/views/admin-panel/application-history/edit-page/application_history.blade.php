<style>
    .table-container {
        max-height: 600px; /* Adjust the height as needed */
        border: 1px solid #ddd; /* Optional border */
        overflow-y: auto;
    }

    .table {
        width: 100%;
        border-collapse: collapse;
    }

    .table th, .table td {
        border: 1px solid #ddd;
        padding: 8px;
        text-align: left;
    }

    .table th {
        background-color: #f4f4f4;
        position: sticky;
        top: 0;
    }

    .table th, .table td {
        width: calc(100% / 3); 
    }
    .modal-dialog.custom-width {
        max-width: 90%; 
    }

    .modal-content {
        width: 100%;
    }
</style>

<form id="application-history" method="POST">
    @csrf

    <div class="table-container">
        <table class="table">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Comments</th>
                    <th>Stage</th>
                    <th>Created By</th>
                </tr>
            </thead>
            <tbody>
                @foreach($Applicationhistories as $key => $value)
                    <tr>
                        <td>{{ DateTimeFormate($value['created_at']) }}</td>
                        <td>
                            @if($value['type']  == "normal")
                                {!! @$value['reason'] !!}
                            @elseif($value['type']  == "email_send")
                                @if (str_word_count(strip_tags($value['email_body'])) > 100)
                                    <div class="email-preview">
                                        <div style="height:70px; overflow:scroll;">
                                            {!! $value['email_body'] !!}
                                        </div>
                                        <a href="javascript:void(0);"
                                           class="read-more"
                                           data-full-text="{{$value['email_body']}}"
                                           data-sender="{{ htmlspecialchars($value['email_send_to'], ENT_QUOTES, 'UTF-8') }}"
                                           data-subject="{{ htmlspecialchars($value['email_subject'], ENT_QUOTES, 'UTF-8') }}">
                                           Full View
                                        </a>
                                    </div>
                                @else
                                    {!! $value['email_body'] !!}
                                @endif
                            @endif
                        </td>
                        <td>{{ @$value['stage_name'] }}</td>
                        <td>{{ @$value['created_by'] }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Email Preview Modal -->
    <div class="modal fade" id="emailPreviewModal" tabindex="-1" aria-labelledby="emailPreviewModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document"> <!-- 'modal-lg' for large modal, adjust if needed -->
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="emailPreviewModalLabel">Email Preview</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button> <!-- Updated class for close button -->
            </div>
            <div class="modal-body">
                <p><strong>Sender:</strong> <span id="emailSender"></span></p>
                <p><strong>Subject:</strong> <span id="emailSubject"></span></p>
                <hr>
                <div id="emailModalBody"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
</form>


<script>
$(document).ready(function() {
    $(document).on('click', '.read-more', function(event) {
        event.preventDefault();

        var fullText = $(this).data('full-text');
        var sender = $(this).data('sender');
        var subject = $(this).data('subject');
        console.log(fullText);
        $('#emailModalBody').html(`${fullText}`);
        $('#emailSender').text(sender);
        $('#emailSubject').text(subject);
        
        $('#emailPreviewModal').modal('show');
    });
});
</script>
