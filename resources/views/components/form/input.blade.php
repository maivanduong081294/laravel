<div {!! $attrs !!}>
    @if($label)
    <div class="form-field-heading">
        <label class="form-field-label" for="{{$id}}">{{$label}}</label>
    </div>
    @endif
    <div class="form-field-body">
        <div class="form-input-wrapper">
            <input {!! $inputAttrs !!}>
            @if($icon)
            <div class="form-input-icon">
                {!!$icon!!}
            </div>
            @endif
        </div>
        @error($name)
            <span class="form-field-error">{{$message}}</span>
        @enderror
    </div>
</div>