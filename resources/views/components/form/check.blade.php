<div {!!$attrs!!}>
    <div class="form-field-heading">
        <label class="form-field-label" for="{{$id}}">
            <input {!!$inputAttrs!!} /><span></span>{{$label}}
        </label>
    </div>
    <div class="form-field-body">
        @error($name)
            <span class="form-field-error">{{$message}}</span>
        @enderror
    </div>
</div>