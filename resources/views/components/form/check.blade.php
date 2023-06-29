<div class="form-input form-input-check">
    <label class="form-input-label" for="{{$id}}">
        <input type="checkbox" id="{{$id}}" {{$checked!='false'?' checked':''}} name="{{$name}}" value="{{$value}}"><span></span>{{$label}}
    </label>
    @error($name)
        <span class="form-input-error">{{$message}}</span>
    @enderror
</div>