
<div {!! $attrs !!}>
    @if($label)
    <div class="form-field-heading">
        <label class="form-field-label">{{$label}}</label>
    </div>
    @endif

    <div class="form-field-body">
        <div class="form-yes-no-wrapper">
            <label for="{{$id}}">
                <input class="no-option" {!!$hiddenAttrs!!}>
                <input class="yes-option" {!!$inputAttrs!!}>
                <i></i>
                <span>{{$no}}</span>
                <span>{{$yes}}</span>
            </label>
        </div>

        @error($name)
            <span class="form-field-error">{{$message}}</span>
        @enderror
    </div>
</div>