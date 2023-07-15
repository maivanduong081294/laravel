
<div {!! $attrs !!}>
    @if($label)
    <div class="form-field-heading">
        <label class="form-field-label" for="{{$id}}">{{$label}}</label>
    </div>
    @endif

    <div class="form-field-body">
        <div class="form-select-wrapper">
            <select {!! $selectAttrs !!}>
                @if ($default)
                    <option value="">{{$default}}</option>
                @endif
                @if ($values)
                    @foreach($values as $key=>$value)
                        @php
                            $key = $keyByValue ? $value : $key;
                        @endphp
                        <option value="{{$key}}" {{in_array($key,$disabled)?" disabled":"" }} {{in_array($key,$selected)?" selected":"" }}>{{$value}}</option>
                    @endforeach
                @endif
            </select>
        </div>

        @error($name)
            <span class="form-field-error">{{$message}}</span>
        @enderror
    </div>
</div>