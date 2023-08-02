<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Validator;

use App\Models\TinyPNG;
use App\Models\Mediable;

class TinyPNGController extends Controller
{
    //

    protected $view_prefix = 'admin.tinypng.';
    private $homeRoute = 'admin.tinypng';
    
    public $title = 'Tiny PNG';
    public $heading = 'Tiny PNG';

    private $apiLimit = 500;

    public function index(Request $request) {
        $tinyPNG = new TinyPNG();

        $actions = self::indexPageActions();
        $listStatus = self::getStatusList();
        $listLimit = self::getLimitList();

        $where = [];
        $search = trim($request->search);
        if($search) {
            $where[] = [
                'api','like','%'.$search.'%',
            ];
        }
        if($request->has('status') && $request->status !== null) {
            $status = $request->status=='1'?1:0;
            $where[] = [
                'status'=>$status
            ];
        }

        if($request->has('limit') && $request->limit !== null) {
            $limitMethod = $request->limit=='1'?'=':'!=';
            $where[] = [
                'month_limit',$limitMethod,date('m'),
            ];
        }

        $order = [];
        $orderBy = trim($request->orderBy);
        $orderType = trim($request->orderType);
        if($orderBy) {
            $orderType = $orderType == 'desc'?'desc':'asc';
            $order[] = [
                'orderBy' => $orderBy,
                'orderType' => $orderType
            ];
        }

        $list = $tinyPNG->getList($where,$order);
        $perPage = $tinyPNG->getPerPage();

        return $this->view('index',compact('list','actions','listStatus','listLimit','perPage'));
    }

    public function add(Request $request) {
        $validate = self::validator($request);
        $tinyPNG = new TinyPNG();
        $tinyPNG->create($request->all());
        return false;
    }

    public function edit(Request $request) {
        $tinyPNG = new TinyPNG();
        $detail = $tinyPNG->getDetailById($request->id);
        if(!$detail) {
            abort(404,'Không tìm thấy API cần thay đổi');
        }
        if(!$request->view) {
            $validate = self::validator($request);
            $request->request->add([
                'month_limit' => 0,                 
                'count' => 0,                 
            ]);
            $tinyPNG->updateById($request->id,$request->all());
            return false;
        }
        else {
            return $this->view('edit',compact('detail'));
        }
    }

    public function update(Request $request) {
        $model = new TinyPNG();
        if($request->has('id')) {
            $detail = $model->find($request->id);
            if($detail) {
                $updated = false;
                if($request->has('status')) {
                    $detail->status = $request->status;
                    $updated = true;
                }
                if($updated) {
                    $detail->save();
                    $request->session()->flash('msg','Cập nhật thành công');
                    $request->session()->flash('type','success');
                    $model->flushCache();
                }
            }
            else {
                abort(404,'Dòng cần cập nhật không tồn tại');
            }
        }
        elseif($request->has('ids')) {
            $ids = $request->ids;
            $list = $model->getListByIds($ids);
            if(empty($list)) {
                abort(404,'Các dòng cần cập nhật không tồn tại');
            }
            else {
                if($request->has('action')) {
                    $action = $request->action;
                    $query = $model->whereIn('id',$ids);
                    switch($action) {
                        case 'enabled_status':
                            $query->update(['status'=>1]);
                            break;
                        case 'disabled_status':
                            $query->update(['status'=>0]);
                            break;
                        default:
                            abort(404,'Hành động không tồn tại');
                    }
                    $request->session()->flash('msg','Cập nhật danh sách thành công');
                    $request->session()->flash('type','success');
                    $model->flushCache();
                }
                else {
                    abort(404,'Vui lòng chọn hành động');
                }
            }
        }
        else {
            abort(404);
        }
    }

    public function delete(Request $request) {
        $model = new TinyPNG();
        if($request->has('id')) {
            $detail = $model->find($request->id);
            if($detail) {
                $model->deleteById($request->id);
                $model->flushCache();
                $request->session()->flash('msg','Xoá thành công');
                $request->session()->flash('type','success');
            }
            else {
                abort(404,'Dòng cần xoá không tồn tại');
            }
        }
        elseif($request->has('ids')) {
            $ids = $request->ids;
            $list = $model->getListByIds($ids);
            if(empty($list)) {
                abort(404,'Các dòng cần xoá không tồn tại');
            }
            else {
                $model->deleteByIds($ids);
                $model->flushCache();
                $request->session()->flash('msg','Xoá danh sách thành công');
                $request->session()->flash('type','success');
            }
        }
        else {
            abort(404);
        }
    }

    public function convertImageToWebp() {
        $tinyPNG = new TinyPNG();
        $mediable = new Mediable();
        $query = $mediable->selectRaw('mediables.id as resize_id, mediables.type as resize_type, media.*');
        $query = $query->leftJoin('media','media.id', '=', 'mediables.media_id');
        $query = $query->where('webp',0);
        $query = $query->orderBy('media.id','asc');
        $detail = $query->first();
        $msg = '';
        if($detail) {
            try {
                $apiDetail = $tinyPNG->getDetail([
                    ['status',1],
                    ['month_limit','!=',date('m')],
                ]);
                if($apiDetail) {
                    $apiKey = $apiDetail->api;
                    echo $apiKey;
                    \Tinify\setKey($apiKey);
                    \Tinify\validate();
                    $compression = \Tinify\compressionCount();
                    if($compression < $this->apiLimit) {
                        $fileName = $detail->file_name;
                        $dir = $detail->disk;
                        $ext = $detail->mime_type;
                        if($detail->resize_type != 'original') {
                            $fileName.= '-'.$detail->resize_type;
                        }
                        $file_dir = public_path($dir.'/'.$fileName.'.'.$ext);
                        echo $file_dir;
                        if(file_exists($file_dir)) {
                            $source = \Tinify\fromFile($file_dir);
                            $converted = $source->convert(array("type" => ["image/webp","image/png"]));
                            $extension = $converted->result()->extension();
                            $check = $source->toFile(public_path($dir).'/'.$fileName.'.'.$extension);
                            if($check) {
                                $msg = 'Success';
                                $mediable->where('id',$detail->resize_id)->update(['webp'=>1,'webp_msg' => $msg]);
                                $newCompression = \Tinify\compressionCount();
                                $tinyPNG->updateById($apiDetail->id,['count' => $newCompression]);
                                if($compression >= $this->apiLimit) {
                                    $tinyPNG->updateById($apiDetail->id,['month_limit' => date('m')]);
                                }
                            }
                            else {
                                $msg = 'Lỗi chuyển đổi';
                                $mediable->where('id',$detail->resize_id)->update(['webp'=>1,'webp_msg' => $msg]);
                            }
                        }
                        else {
                            $msg = 'Không tìm thấy hình ảnh: '.$file_dir;
                            $mediable->where('id',$detail->resize_id)->update(['webp'=>1,'webp_msg' => $msg]);
                        }
                    }
                    else {
                        $msg = 'API đã hết lượt. Thử lại sau';
                        $tinyPNG->updateById($apiDetail->id,['month_limit' => date('m')]);
                    }
                }
                else {
                    $msg = 'Không tìm thấy API | API đã hết lượt | API không hoạt động';
                }
            } catch(\Tinify\Exception $e) {
                $msg = $e->getMessage();
                $mediable->where('id',$detail->resize_id)->update(['webp'=>2,'webp_msg' => $msg]);
            }
        }
        else {
            $msg = 'Empty';
        }
        return $msg;
    }

    private function indexPageActions() {
        return [
            'enabled_status' => 'Hoạt động',
            'disabled_status' => 'Không hoạt động',
            'delete' => 'Xoá API',
        ];
    }

    private function getLimitList() {
        return [
            0 => "Chưa tới giới hạn",
            1 => "Đã tới giới hạn",
        ];
    }

    private function getStatusList() {
        return [
            0 => "Không hoạt động",
            1 => "Hoạt động",
        ];
    }

    public function validator(Request $request) {
        $rules = [
            'api' => ['required','unique:api_tinypng,api',function($attribute,$value,$fail){
                try {
                    \Tinify\setKey($value);
                    \Tinify\validate();
                } catch(\Tinify\Exception $e) {
                    $msg = $e->getMessage();
                    $fail($msg);
                }
            }]
        ];

        $messages = [
            'required' => ':attribute không thể để trống',
            'unique' => ':attribute đã tồn tại trong hệ thống'
        ];

        $attributes = [
            'api' => 'API',
        ];
        $validate = Validator::make($request->all(),$rules,$messages,$attributes);
        return $validate->validate();
    }
}
