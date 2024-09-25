<style>
    .file-list-item {
        display: flex;
        align-items: center;
        border-top: 1px solid rgb(187, 175, 175);
        margin-bottom: 5px;
        width: 100%;
    }

    .file-list-item img {
        width: 20%;
        max-width: 50px;
        padding: 5px;
    }

    .file-list-item .file-name {
        flex: 1;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
        padding-left: 20px;
    }

    .file-list-item button {
        margin-top: 10px;
        width: 100px;
        margin-left: auto;
    }

    #uploaded-docs-list {
        border-radius: 10px;
    }

    .documents-preview {
        display: none;
    }

    #upload-button {
        display: none;
        margin-top: 10px;
    }
    #upload-button_new{
 margin-top: 5px;
    }
</style>

<div class="row">
    <div class="mt-1 mb-1 col-12 col-md-6 col-lg-4 col-xl-4">
        <div class="justify-content-center " style="display: ruby-text; border: 1px dashed black; border-radius:10px; padding:10px; justify-content:center;">
            <p style="justify-content: center; text-align:center;">Please Upload only COLOR SCAN COPY
                <br>
                Drag Files & Drop Here</p>
            <input type="file" name="documents[]" id="documents" class="form-control" accept=".pdf, .doc, .docx, image/*" multiple style="display: none;">
            <button type="button" onclick="selectFiles()" class="btn" style="border: 1px solid black;"> 
                <img src="{{ asset('assets/img/website/upload_icon.png') }}" style="width:20px; margin-right:20px;"> Select Files
            </button>

            <div class="mt-2 mb-1 col-12 col-md-12 col-lg-12 col-xl-12 documents-preview">
                <h5>Documents Preview</h5>
                <ul id="uploaded-docs-list" class="list-unstyled"></ul>
                <button id="upload-button" class="btn btn-primary" onclick="uploadFiles()">Upload Files</button>
            </div>
        </div>
    </div>

    <div class="mt-1 mb-1 col-12 col-md-6 col-lg-8 col-xl-8" style="border: 1px dashed black; border-radius:10px;">
        <div class="mt-2 mb-1 col-12 col-md-12 col-lg-12 col-xl-12 ">
            <h5>Documents Uploaded</h5>
            <ul id="uploaded-docs-list" class="list-unstyled">
                @foreach($documents_list as $key => $value)
                   <li class="file-list-item">
                   <img src="{{ asset('admin_assets\images\student_documents') }}/{{ $value['stored_name']}}">
                   <a href="{{ asset('admin_assets\images\student_documents') }}/{{ $value['stored_name']}}" target="_blank"
                        class="file-name">{{ $value['original_name']}}</a> <a style="color:white;" target="_blank" href="{{ asset('admin_assets\images\student_documents') }}/{{ $value['stored_name']}}"><span class="btn btn-primary" id="upload-button_new">View</span></a></li>
                @endforeach
            </ul>
        </div>
    </div>
</div>


<script>
    var selectedFiles = []; // Move selectedFiles to the global scope

    $(document).ready(function() {
        $('#documents').on('change', function() {
            handleFilePreview(this);
            updateFileList();
        });

        function handleFilePreview(input) {
            const files = input.files;
            selectedFiles = Array.from(files);

            $.each(selectedFiles, function(index, file) {
                const fileType = file.type;
                const fileName = file.name;

                if (fileType.startsWith('image/')) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        const img = $('<img>', {
                            src: e.target.result,
                            alt: fileName,
                            title: fileName
                        });
                    };
                    reader.readAsDataURL(file);
                } else if (fileType === 'application/pdf') {
                    const pdfIcon = $('<img>', {
                        src: '{{ asset('assets/img/website/pdf.png') }}',
                        alt: fileName,
                        title: fileName
                    });
                } else if (fileType === 'application/msword' || fileType === 'application/vnd.openxmlformats-officedocument.wordprocessingml.document') {
                    const docIcon = $('<img>', {
                        src: '{{ asset('assets/img/website/pdf.png') }}',
                        alt: fileName,
                        title: fileName
                    });
                }
            });
        }

        function updateFileList() {
            $('.documents-preview').show();
            const listContainer = $('#uploaded-docs-list');
            listContainer.empty();

            $.each(selectedFiles, function(index, file) {
                const fileType = file.type;
                const fileName = file.name;
                const fileUrl = URL.createObjectURL(file);

                const listItem = $('<li>', {
                    class: 'file-list-item'
                });

                if (fileType.startsWith('image/')) {
                    const img = $('<img>', {
                        src: URL.createObjectURL(file),
                        alt: fileName
                    });
                    listItem.append(img);
                } else if (fileType === 'application/pdf') {
                    const pdfIcon = $('<img>', {
                        src: '{{ asset('assets/img/website/pdf.png') }}',
                        alt: fileName
                    });
                    listItem.append(pdfIcon);
                } else if (fileType === 'application/msword' || fileType === 'application/vnd.openxmlformats-officedocument.wordprocessingml.document') {
                    const docIcon = $('<img>', {
                        src: '{{ asset('assets/img/website/pdf.png') }}',
                        alt: fileName
                    });
                    listItem.append(docIcon);
                }

                const link = $('<a>', {
                    href: fileUrl,
                    text: fileName,
                    target: '_blank',
                    class: 'file-name'
                });
                listItem.append(link);

                const removeButton = $('<button>', {
                    html: `<img src="{{ asset('assets/img/website/delete_icon.png') }}" style="width: 100%; height: auto; ">`,
                    class: 'btn',
                    style: 'width: 30px; height: auto; padding:0px;',
                    click: function() {
                        selectedFiles.splice(index, 1);
                        updateFileList();
                        handleFilePreview({
                            files: selectedFiles
                        });
                    }
                });

                listItem.append(removeButton);
                listContainer.append(listItem);
            });

            if (selectedFiles.length > 0) {
                $('#upload-button').show();
            } else {
                $('#upload-button').hide();
            }
        }
    });

    function selectFiles() {
        document.getElementById('documents').click();
    }

    function uploadFiles() {
        let formData = new FormData();


        $.each(selectedFiles, function(index, file) {
            formData.append('documents[]', file);
        });

        formData.append('_token', "{{ csrf_token() }}");
         formData.append('application_id', "{{ @$application_id }}");
        

        $.ajax({
            url: '{{ route('application.upload_student_document') }}', // Ensure this route is defined in your routes file
            type: 'POST',
            data: formData,
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
                GetUploadDownload(); // Assuming this function refreshes the student information on the page
            },
            error: function(xhr) {
                Swal.close();
                const errors = xhr.responseJSON.errors;
                if (errors) {
                    // Clear previous errors
                    $('#documents').removeClass('is-invalid');
                    $('.invalid-feedback').removeClass('d-block');

                    // Display new errors
                    for (const [field, messages] of Object.entries(errors)) {
                        const input = $(`#${field}`);
                        if (input.length) {
                            input.addClass('is-invalid');
                            const errorDiv = input.next('.invalid-feedback');
                            errorDiv.html(messages.join('<br>'));
                            errorDiv.addClass('d-block');
                        }
                    }
                }
            }
        });
    }
</script>
