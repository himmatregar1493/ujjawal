<style>
    /* Basic styles for pagination */
    .pagination-wrapper {
        display: flex;
        justify-content: center;
        align-items: center;
        margin: 20px 0;
    }

    .pagination-link {
        text-decoration: none;
        color: #007bff;
        padding: 8px 16px;
        margin: 0 4px;
        border: 1px solid #007bff;
        border-radius: 4px;
        font-size: 14px;
    }

    .pagination-link:hover {
        background-color: #007bff;
        color: white;
    }

    .pagination-current {
        padding: 8px 16px;
        margin: 0 4px;
        border: 1px solid #007bff;
        border-radius: 4px;
        font-size: 14px;
        background-color: #007bff;
        color: white;
    }

    .pagination-disabled {
        padding: 8px 16px;
        margin: 0 4px;
        border: 1px solid #ddd;
        border-radius: 4px;
        font-size: 14px;
        color: #ddd;
    }

    /* Responsive styles for screens larger than 700px */
    @media (max-width: 700px) {
        .pagination-link,
        .pagination-current,
        .pagination-disabled {
            padding: 5px 10px;
            margin: 0 6px;
            font-size: 12px;
        }
    }
</style>

@if($totalPage > 0)
    <div class="pagination-wrapper">
        @if ($CurrentPage == 1)
            <span class="pagination-disabled">«</span>
        @else
            <button class="pagination-link btn pagignation-btn m-1" data-id="{{ $CurrentPage-1 }}" data-varibale_name="page">«</button>
        @endif

        @php
            $start = max(1, $CurrentPage - 1);
            $end = min($totalPage, $CurrentPage + 1);
        @endphp

        @if ($start > 1)
            <button class="pagination-link btn pagignation-btn m-1" data-id="1" data-varibale_name="page">1</button>
            @if ($start > 2)
                <span class="pagination-disabled">...</span>
            @endif
        @endif

        @for ($i = $start; $i <= $end; $i++)
            @if ($i == $CurrentPage)
                <span class="pagination-current">{{ $i }}</span>
            @else
                <button class="pagination-link btn pagignation-btn m-1" data-id="{{ $i }}" data-varibale_name="page">{{ $i }}</button>
            @endif
        @endfor

        @if ($end < $totalPage)
            @if ($end < $totalPage - 1)
                <span class="pagination-disabled">...</span>
            @endif
            <button class="pagination-link btn pagignation-btn m-1" data-id="{{ $totalPage }}" data-varibale_name="page">{{ $totalPage }}</button>
        @endif

        @if ($CurrentPage == $totalPage)
            <span class="pagination-disabled">»</span>
        @else
            <button class="pagination-link btn pagignation-btn m-1" data-id="{{ $CurrentPage+1 }}" data-varibale_name="page">»</button>
        @endif
    </div>
@endif
