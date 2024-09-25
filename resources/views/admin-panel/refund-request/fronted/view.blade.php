<style>
    .custom-img {
        width: 100%;
        height: auto;
        object-fit: cover;
        border-radius: 20px;
    }

    .required-label::after {
        content: '*';
        color: red;
        margin-left: 5px;
    }

    .loader-image-design {
        width: 50px;
        justify-content: center;
        margin: auto;
    }

    #detail-page {
        justify-content: center;
    }

    .card {
        border-radius: 10px;
        overflow: hidden;
        transition: transform 0.3s ease;
    }

    .card:hover {
        transform: translateY(-5px);
    }

    .card img {
        width: auto;
        height: 150px;
        object-fit: contain;
        margin: 20px auto;
        display: block;
    }

    .card-title {
        font-size: 1.2rem;
        font-weight: 600;
    }

    .modal-iframe {
        width: 100%;
        height: 500px;
        border: none;
        display: none;
    }

    .modal-loader {
        display: none;
        text-align: center;
    }

    .modal-loader img {
        width: 50px;
    }

    .btn-success {
        background-color: #28a745;
        border-color: #28a745;
    }

    .btn-success:hover {
        background-color: #218838;
        border-color: #1e7e34;
    }
</style>
    @if(count($documents) > 0)
<div class="row row-cols-1 row-cols-md-2 row-cols-lg-4 g-4">
    @foreach($documents as $document)
    
        <div class="col">
            <div class="card text-center">
                <img src="@if($document['university_name'] != "") {{ $document['university_logo'] }} @else {{ $document['path'] }} @endif" class="card-img-top custom-img" alt="{{ $document['name'] }}">
                <div class="card-body">
                @if($document['university_name'] != "") <h5 class="card-title">{{ $document['university_name'] }}</h5> @else <h5 class="card-title">{{ $document['name'] }}</h5>  @endif
                    
                    {{-- <p class="card-text">View {{ strtoupper($document['extension']) }} Document</p> --}}
                    <button class="btn btn-success" onclick="openDocument('{{ $document['link'] }}', '{{ $document['extension'] }}')">View</button>
                </div>
            </div>
        </div>
    @endforeach
    </div>
   @else
        <center>No data Found</center>
    @endif


<!-- Modal -->


<script>
    function openDocument(link, extension) {
        $('#documentModal').modal('show');
        let iframe = document.getElementById('documentFrame');
        let loader = document.getElementById('modalLoader');

        // Display loader and hide iframe initially
        loader.style.display = 'block';
        iframe.style.display = 'none';

        // Determine iframe source based on file extension
        if (extension === 'pdf') {
            iframe.src = link;
        } else if (['doc', 'docx', 'xlsx', 'xls'].includes(extension)) {
            iframe.src = 'https://view.officeapps.live.com/op/embed.aspx?src=' + encodeURIComponent(link) + '&embedded=true';
        }else if(['ppt','pptx']){
            iframe.src = 'https://view.officeapps.live.com/op/embed.aspx?src=' + encodeURIComponent(link) + '&embedded=true';
        } else if (['jpg', 'jpeg', 'png'].includes(extension)) {
            iframe.src = link;
        } else {
            iframe.src = link;
        }

        // Show the modal


        // Listen for iframe load event to hide loader and show iframe
        iframe.onload = function() {
            loader.style.display = 'none';
            iframe.style.display = 'block';
        };
    }
</script>
