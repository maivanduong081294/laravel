<label class="{{$class}} style-checkbox"  {{$id?('for="'.$id.'"'):''}}>
    <input type="checkbox" {{$id?('id="'.$id.'"'):''}} {{$checked!='false'?' checked':''}}  {{$name?('name="'.$name.'"'):''}} {{$value?('value='.$value):''}}><span></span>{{$label}}
</label>