<div class="table-data">
    <table class="table">
        <thead>
            <tr>
                <th class="text-center"><x-styles.checkbox type="checkbox" class="checkall"/></th>
                <th>Title</th>
                <th>Uri</th>
                <th>Controller</th>
                <th>Function</th>
                <th>Methods</th>
                <th>Middleware</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @if($list->count()<=0)
            <tr>
                <th colspan="8">Không có dữ liệu</th>
            </tr>
            @else
            @foreach($list as $item)
                <tr>
                    <td class="text-center" data-title="Chọn"><x-styles.checkbox type="checkbox" class="check" value="{{$item->id}}"/></th>
                    <td data-title="Title">
                        <a href="{{route('admin.routes.edit',['id'=>$item->id])}}">{{$item->title}}</a>
                    </td>
                    <td data-title="Uri">{{$item->uri}}</td>
                    <td data-title="Controller">{{$item->controller}}</td>
                    <td data-title="Function">{{$item->function}}</td>
                    <td data-title="Methods">{{$item->method}}</td>
                    <td data-title="Middleware">{{$item->middleware}}</td>
                    <td data-title="Status"><x-styles.status class="main" value="{{$item->status}}" /></td>
                </tr>
            @endforeach
            @endif
        </tbody>
        <tfoot>
            <tr>
                <th class="text-center"><x-styles.checkbox type="checkbox" class="checkall"/></th>
                <th>Title</th>
                <th>Uri</th>
                <th>Controller</th>
                <th>Function</th>
                <th>Methods</th>
                <th>Middleware</th>
                <th>Status</th>
            </tr>
        </tfoot>
    </table>
</div>
{{ $list->links() }}
