<form id="edit-item-ajax-form" action="{{route('admin.upload.edit',['id'=>$detail->id])}}" enctype="multipart/form-data">
    <div class="edit-upload-wrapper">
        <div class="edit-upload-left">
            <figure>
                {!! getImageById($detail->id,'original') !!}
            </figure>
        </div>
        <div class="edit-upload-right">
            <div class="file-information">
                <table>
                    <tr>
                        <td>Đã tải lên vào lúc:</td>
                        <td>{{date('d/m/Y H:i:s',strtotime($detail->created_at))}}</td>
                    </tr>
                    <tr>
                        <td>Người tải lên:</td>
                        <td>{{$author->fullname}}</td>
                    </tr>
                    <tr>
                        <td>Tên tập tin:</td>
                        <td>{{$detail->file_name.'.'.$detail->ext}}</td>
                    </tr>
                    <tr>
                        <td>Loại tập tin:</td>
                        <td>{{$detail->mime_type}}</td>
                    </tr>
                    <tr>
                        <td>Dung lượng tệp:</td>
                        <td>{{$detail->amount}}</td>
                    </tr>
                    <tr>
                        <td>Kích thước:</td>
                        <td>{{$detail->dimension}}</td>
                    </tr>
                </table>
            </div>
            <div class="image-update-fields">
                <x-form.input value="" name="name" label="Tiêu đề" value="{{$detail->name}}" placeholder="Tiêu đề" />
                <x-styles.button content="Cập nhật"/>
            </div>
            <div class="image-edit-actions">
                <x-styles.button href="{{getImageSourceById($detail->id,'original')}}" target="_blank" content='<i class="fa-solid fa-eye"></i>'/>
                <x-styles.button type="button" class="btn-link" content='<i class="fa-solid fa-link"></i>'/>
                <x-styles.button href="{{getImageSourceById($detail->id,'original')}}" download="{{$detail->file_name}}" class="btn-download" content='<i class="fa-solid fa-download"></i>'/>
                <x-styles.button type="button" class="btn-delete-data btn-danger" data-item-id="{{$detail->id}}" content='<i class="fa-solid fa-trash"></i>'/>
            </div>
        </div>
    </div>
</form>