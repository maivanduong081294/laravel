<div {!! $attrs !!}>
    @if($label)
    <label class="form-input-label" for="{{$id}}">{{$label}}</label>
    @endif
    <div class="form-input-wrapper">
        <input {!! $inputAttrs !!}>
        @if($icon)
        <div class="form-input-icon">
            {!!$icon!!}
        </div>
        @endif
    </div>
    @error($name)
        <span class="form-input-error">{{$message}}</span>
    @enderror
</div>