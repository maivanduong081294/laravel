@if ($values)
<div {!! $attrs !!}>
    @if($label)
    <label class="form-input-label" for="{{$id}}">{{$label}}</label>
    @endif
    <div class="form-select-wrapper">
        <select {!! $selectAttrs !!}>
            @if ($default)
                <option value="">{{$default}}</option>
            @endif
            @foreach($values as $key=>$value)
                @php
                    $key = $keyByValue ? $value : $key;
                @endphp
                <option value="{{$key}}" {{in_array($key,$selected)?" selected":"" }}>{{$value}}</option>
            @endforeach
        </select>
    </div>

    @error($name)
        <span class="form-input-error">{{$message}}</span>
    @enderror
</div>
@endif