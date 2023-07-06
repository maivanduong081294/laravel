@if ($values)
    <select {!!$attrs!!}>
        @if ($default)
            <option value="">{{$default}}</option>
        @endif
        @foreach($values as $key=>$value)
            <option value="{{$keyByValue ? $value : $key}}" {{$selected == $value?" selected":"" }}>{{$value}}</option>
        @endforeach
    </select>
@endif