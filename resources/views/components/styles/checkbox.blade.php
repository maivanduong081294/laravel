<label {!!$labelAttrs!!}>
    <input type="{{$type}}" {!!$checkboxAttrs!!} value="{{$value}}" /><i></i>
    @if ($label)
        <span>{{$label}}</span>
    @endif
</label>