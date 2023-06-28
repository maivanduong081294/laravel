<div class="form-input">
    @if($label)
    <label class="form-input-label" for="{{$id}}">{{$label}}</label>
    @endif
    <div class="form-input-wrapper">
        <input type="{{$type}}" id="{{$id}}" name="{{$name}}" placeholder="{{$placeholder}}" value="{{$value}}">
        @if($icon)
        <div class="form-input-icon">
            {!!$icon!!}
        </div>
        @endif
    </div>
</div>