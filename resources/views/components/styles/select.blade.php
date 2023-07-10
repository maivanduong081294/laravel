@if ($values)
    <select {!!$attrs!!}>
        @if ($default)
            <option value="">{{$default}}</option>
        @endif
        @foreach($values as $key=>$value)
            @php
                $key = $keyByValue ? $value : $key;
            @endphp
            <option value="{{$key}}" {{$selected == $key?" selected":"" }}>{{$value}}</option>
        @endforeach
    </select>
@endif