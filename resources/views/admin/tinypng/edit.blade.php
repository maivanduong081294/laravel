<form id="edit-item-ajax-form" action="{{route('admin.tinypng.edit',['id'=>$detail->id])}}" enctype="multipart/form-data">
    <x-form.input value="" name="api" label="API" value="{{$detail->api}}" placeholder="API" />
    <x-styles.button content="Cập nhật"/>
</form>