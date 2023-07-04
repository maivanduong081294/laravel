@if ($paginator->hasPages())
@php
    $show = 5;
    $current = $paginator->currentPage();
    $total = $paginator->total();
    if(count($elements) == 1) {
        $list = $elements[0];
        $first = $list[1];
        $last = $list[$total];
    }
    else {
        $first = $elements[0][1];
        $last = $elements[4][$total];
        if(isset($elements[2]) && count($elements[2]) >  count($elements[0])) {
            $list = $elements[2];
        }
        elseif(count($elements[4]) >  count($elements[0])) {
            $list = $elements[4];
        }
        else {
            $list = $elements[0];
        }
    }
    $start = 1;
    $padding =  (ceil($show / 2) - 1);
    $totalShow = $padding * 2;
    $show = $totalShow + 1;
    if($current > $padding && $current <= $total - $totalShow) {
        $start = $current - $padding;
    }
    elseif($current <=  $totalShow) {
        $start = 1;
    }
    elseif($current > $total - $totalShow) {
        $start = $total - $totalShow;
    }
    $limit = $show;
    if($limit + $start > $total) {
        $limit = $total - $start + 1;
    }
@endphp
    <div class="pagination-wrapper">
        <ul class="pagination">
            {{-- Previous Page Link --}}
            @if ($paginator->onFirstPage())
                <li class="disabled">
                    <span><i class="fa-solid fa-angles-left"></i></span>
                </li>
                <li class="disabled">
                    <span><i class="fa-solid fa-angle-left"></i></span>
                </li>
            @else
                <li>
                    <a href="{{ $first }}">
                        <i class="fa-solid fa-angles-left"></i>
                    </a>
                </li>
                <li>
                    <a href="{{ $paginator->previousPageUrl() }}">
                        <i class="fa-solid fa-angle-left"></i>
                    </a>
                </li>
            @endif

            {{-- Pagination Elements --}}
            @for ($i = $start; $i < $limit + $start; $i++)
                @if ($i == $paginator->currentPage())
                    <li class="actived"><span>{{ $i }}</span></li>
                @else
                    <li><a href="{{ $list[$i] }}">{{ $i }}</a></li>
                @endif
            @endfor

            {{-- Next Page Link --}}
            @if ($paginator->hasMorePages())
                <li>
                    <a href="{{ $paginator->nextPageUrl() }}">
                        <i class="fa-solid fa-angle-right"></i>
                    </a>
                </li>
                <li>
                    <a href="{{ $last }}">
                        <i class="fa-solid fa-angles-right"></i>
                    </a>
                </li>
            @else
                <li class="disabled">
                    <span><i class="fa-solid fa-angle-right"></i></span>
                </li>
                <li class="disabled">
                    <span><i class="fa-solid fa-angles-right"></i></span>
                </li>
            @endif
        </ul>
    </div>
@endif
