@foreach ($filters as $filter)
    <button type="button" class="btn filter-btn m-1" style="color: {{ $filter['color'] }}; min-width:130px; display:inline; background-color: {{ $filter['background-color'] }};" data-id="{{ $filter['name'] }}" data-varibale_name="filter_btn">
        {{ $filter['label'] }}
    </button>
@endforeach
