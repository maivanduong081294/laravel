@if ($content)
    <{{$comp}} {!!$attrs!!}>
        @if ($leftIcon)
            <span class="left-icon">{!!$leftIcon!!}</span>
        @endif
        <span>{!!$content!!}</span>
        @if ($rightIcon)
            <span class="right-icon">{!!$rightIcon!!}</span>
        @endif
    </{{$comp}}>
@endif