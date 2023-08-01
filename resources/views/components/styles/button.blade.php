@if ($content)
    <{{$comp}} {!!$attrs!!} {{ $attributes }}>
        @if ($leftIcon)
            <span class="left-icon">{!!$leftIcon!!}</span>
        @endif
        <span>{!!$content!!}</span>
        @if ($rightIcon)
            <span class="right-icon">{!!$rightIcon!!}</span>
        @endif
    </{{$comp}}>
@endif