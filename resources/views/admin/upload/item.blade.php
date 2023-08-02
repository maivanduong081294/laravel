<div class="grid-data-item image-upload-item">
    <div class="grid-data-item-wrapper">
        <figure class="image-wrapper">
            <img src="{{getImageSourceById($item->id)}}" data-image="{{getImageSourceById($item->id,'original')}}" alt="{{$item->name}}" />
        </figure>
        <div class="image-upload-item-actions">
            <div class="image-upload-item-actions-list">
                <x-styles.button href="javascript:void(0)" class="btn-icon btn-view btn-primary" content='<i class="fa-solid fa-eye"></i>' />
                <x-styles.button href="javascript:void(0)" class="btn-icon btn-link btn-primary" content='<i class="fa-solid fa-link"></i>' />
                <x-styles.button href="{{route('admin.upload.edit',[$item->id])}}" class="btn-icon btn-edit edit-item-ajax btn-primary" content='<i class="fa-solid fa-pen"></i>' />
                <x-styles.button href="javascript:void(0)" class="btn-icon btn-delete-data btn-danger" data-item-id="{{$item->id}}" content='<i class="fa-solid fa-trash"></i>' />
            </div>
        </div>
    </div>
</div>