<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Http\Controllers\Admin\Controller;

use Validator;
use File;
use Intervention\Image\Facades\Image;

use App\Models\Media;
use App\Models\Mediable;
use App\Models\User;
use App\Models\Option;

class UploadController extends Controller
{
    //
    protected $view_prefix = 'admin.upload.';
    private $homeRoute = 'admin.upload';

    public $title = 'Thư viện hình ảnh';

    public function index(Request $request) {
        $this->heading = 'Thư viện hình ảnh';

        $media = new Media();

        $where = [];

        $search = trim($request->search);
        if($search) {
            $where[] = [
                ['file_name','like','%'.$search.'%',],
                ['name','like','%'.$search.'%',]
            ];
        }

        $order = [];
        $list = $media->getList($where,$order);
        $perPage = $media->getPerPage();

        $users = (array) $media->getListUser();

        $actions = self::indexPageActions();

        return $this->view('index',compact('list','users','actions','perPage'));
    }

    public function add(Request $request) {

        $validator = $this->validator($request);
        if($validator->fails()) {
            return back()->withErrors($validator)->withInput()->with('msg','Tải ảnh lên thất bại');
        }

        $type = $request->type?$request->type:'upload';
        $name = $request->name?$request->name:uniqid();
        $time = time();
        $fileName = Str::slug($name).'-'.$time;
        $ext = $request->image->extension();
        $mimeType = $request->file->getClientMimeType();

        $dir = 'uploads/'.date('Y').'/'.date('m');
        $imageName = $fileName.'.'.$ext;
        $fileDir =  $dir.'/'.$imageName;

        $request->image->move(public_path($dir), $imageName);  

        if(file_exists($fileDir)) {
            $media = new Media();
            $user = User::getCurrentUser();
            $data = [
                'name' => $name,
                'file_name' => $fileName,
                'disk' => $dir,
                'mime_type' => $ext,
                'author_id' => $user->id,
                'type' => $type,
            ];
            $imageDetail = $media->create($data);
            if($imageDetail) {
                $option = new Option();
                $mediable = new Mediable();
                $resizes = $option->getAllList([
                    ['type'=>'images'],
                    ['sub_type'=>$type],
                ]);

                if(!empty($resizes)) {
                    foreach($resizes as $resize) {
                        $image = Image::make($fileDir);
                        $dimension = explode('x',$resize->value);
                        if(count($dimension) >= 2) {
                            $width = $dimension[0]?$dimension[0]:null;
                            $height = $dimension[1]?$dimension[1]:null;
                            $crop = isset($dimension[2]) && $dimension[2] > 0?true:false;
                            if($width + $height > 0) {
                                $newFile = $dir.'/'.$fileName.'-'.$resize->key.'.'.$ext;
                                if($crop) {
                                    $image->fit($width, $height, function ($constraint) {
                                        $constraint->upsize();
                                    })->save($newFile);
                                }
                                else {
                                    $image->resize($width, $height, function ($constraint) {
                                        $constraint->aspectRatio();
                                        $constraint->upsize();
                                    })->save($newFile);
                                }
                                $data = [
                                    'media_id' => $imageDetail->id,
                                    'type' => $resize->key,
                                ];
                                $mediable->create($data);
                            }
                        }
                    }
                    $data = [
                        'media_id' => $imageDetail->id,
                        'type' => 'original',
                    ];
                    $mediable->create($data);
                }
            }
        }
        /* Store $imageName name in DATABASE from HERE */
    
        return back()
            ->with('msg','Bạn đã tải ảnh lên thành công')
            ->with('type','success'); 
    }

    public function edit(Request $request) {
        $media = new Media();
        $user = new User();
        $detail = $media->getDetailById($request->id);
        if(!$detail) {
            abort(404,'Không tìm thấy hình ảnh cần thay đổi');
        }
        $author = $user->getDetailById($detail->author_id);
        $fileName = $detail->file_name;
        $dir = $detail->disk;
        $ext = $detail->mime_type;
        $fileDir = public_path($dir.'/'.$fileName.'.'.$ext);
        $detail->amount = formatBytes(File::size($fileDir));
        $detail->mime_type = File::mimeType($fileDir);
        $detail->ext = $ext;

        $data = getimagesize($fileDir);
        $width = $data[0];
        $height = $data[1];
        $detail->dimension = $width.'x'.$height;
        if(!$request->view) {
            $media->updateById($request->id,['name'=>$request->name]);
            return false;
        }
        else {
            return $this->view('edit',compact('detail','author'));
        }
    }

    public function delete(Request $request) {
        $model = new Media();
        $mediable = new Mediable();
        if($request->has('id')) {
            $detail = $model->find($request->id);
            if($detail) {
                $where = [
                    ['media_id',$detail->id],
                ];
                $resizes = $mediable->getAllList($where);
                $fileName = $detail->file_name;
                $dir = $detail->disk;
                $ext = $detail->mime_type;
                $files = [];
                if(count($resizes)) {
                    foreach($resizes as $resize) {
                        $tempName = $fileName;
                        if($resize->type != 'original') {
                            $tempName.= '-'.$resize->type;
                        }
                        $files[] = public_path($dir.'/'.$tempName.'.webp');
                        $files[] = public_path($dir.'/'.$tempName.'.'.$ext);
                    }
                }
                foreach($files as $file) {
                    if(file_exists($file)){
                        echo '1';
                        unlink($file);
                    }
                }
                $mediable->deleteByWhere($where);
                $mediable->flushCache();
                $model->deleteById($request->id);
                $model->flushCache();
                $request->session()->flash('msg','Xoá thành công');
                $request->session()->flash('type','success');
            }
            else {
                abort(404,'Phân quyền không tồn tại');
            }
        }
        // elseif($request->has('ids')) {
        //     $ids = $request->ids;
        //     $where = [
        //         [
        //             'super_admin',
        //             '!=',
        //             '1',
        //         ]
        //     ];
        //     $list = $model->getListByIds($ids,$where);
        //     if(empty($list)) {
        //         $all = $model->getListByIds($ids);
        //         if(!empty($all)) { 
        //             abort(403,'Bạn không thể xoá những phân quyền đã chọn');
        //         }
        //         else {
        //             abort(404,'Phân quyền không tồn tại');
        //         }
        //     }
        //     else {
        //         $model->deleteByIds($ids,$where);
        //         $model->flushCache();
        //         $request->session()->flash('msg','Xoá danh sách thành công');
        //         $request->session()->flash('type','success');
        //     }
        // }
        else {
            abort(404);
        }
    }

    private function indexPageActions() {
        return [
            'delete' => 'Xoá hình ảnh',
        ];
    }

    private function validator($request) {
        $rules = [
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
        ];
        $messages = [
            'image.required' => 'Không thể bỏ trống hình ảnh',
            'image.image' => 'Hình ảnh không đúng định dạng (JPEG, JPG, PNG, GIF, SVG, WEBP)',
            'image.max' => 'Hình ảnh không được quá :max KB',
        ];

        $validator = Validator::make($request->all(),$rules,$messages);
        return $validator;
    }
}
