<div {!!$attrs!!}>
    @if($label)
    <div class="form-field-heading">
        <label class="form-field-label" for="{{$id}}">{{$label}}</label>
    </div>
    @endif
    <div class="form-field-body">
        <div class="form-check-list">
            @if ($all)
            <label class="select-all" for="{{$name.'-all'}}">
                <input {!!$inputAttrs!!} id="{{$name.'-all'}}" value="" /><i></i>
                <span>Tất cả</span>
            </label>
            @endif
            @php
            $i = 1;
            @endphp
            @foreach ($values as $key=>$item)
                @php
                    $key = $keyByValue ? $item : $key;
                @endphp
                <label for="{{$name.'-'.$i}}">
                    <input {!!$inputAttrs!!} id="{{$name.'-'.$i}}" value="{{$key}}" {{in_array($key,$selected)?" checked":"" }}/><i></i>
                    <span>{{$item}}</span>
                </label>
                @php
                $i++;
                @endphp
            @endforeach
        </div>
        @error($name)
            <span class="form-field-error">{{$message}}</span>
        @enderror
    </div>
</div>