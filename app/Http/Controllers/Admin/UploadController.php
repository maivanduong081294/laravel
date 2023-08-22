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

        if( $validator->fails() ) {
            $messages = $validator->errors()->getMessages();
            //dd($messages);
            foreach($messages as $value) {
                $message = $value[0];
                break;
            }
            return response()->json(['error'=>$message])->setStatusCode(400);
        }

        $type = $request->type?$request->type:'upload';
        $name = $request->name?$request->name:uniqid();
        $time = time();
        $fileName = Str::slug($name).'-'.$time;
        $ext = $request->file->extension();

        $dir = 'uploads/'.date('Y').'/'.date('m');
        $imageName = $fileName.'.'.$ext;
        $fileDir =  $dir.'/'.$imageName;

        $request->file->move(public_path($dir), $imageName);  

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
            if($imageDetail && !in_array($ext,["svg","gif"])) {
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
    
        $item = $imageDetail;
        return $this->view('item',compact('item'));
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

        
        if($ext == 'svg') {
            $svgfile = simplexml_load_file($fileDir);
            $viewBox = explode(' ', $svgfile['viewBox']);
            if($viewBox) {
                $width = $viewBox[2];
                $height = $viewBox[3];
            }
            else {
                $width = substr($svgfile["width"],0,-2);
                $height = substr($svgfile["height"],0,-2);
            }
            $detail->dimension = $width.'x'.$height;
        }
        else {
            $sizes = getimagesize($fileDir);
            if($sizes) {
                $width = $sizes[0];
                $height = $sizes[1];
                $detail->dimension = $width.'x'.$height;
            }
            else {
                $detail->dimension = 0;
            }
        }
        if(!$request->view) {
            $media->updateById($request->id,['name'=>$request->name]);
            return false;
        }
        else {
            return $this->view('edit',compact('detail','author'));
        }
    }

    public function delete(Request $request) {
        if($request->has('id')) {
            $result = self::deleteByItemId($request->id);
            if($result) {
                $request->session()->flash('msg','Xoá thành công');
                $request->session()->flash('type','success');
            }
            else {
                abort(404,'Phân quyền không tồn tại');
            }
        }
        elseif($request->has('ids')) {
            $ids = $request->ids;
            $flag = false;
            foreach($ids as $id) {
                $result = self::deleteByItemId($id);
                if(!$flag && $result) {
                    $flag = $result;
                }
            }
            if($flag) {
                $request->session()->flash('msg','Xoá danh sách thành công');
                $request->session()->flash('type','success');
            }
            else {
                $request->session()->flash('msg','Xoá danh sách không thành công');
            }
        }
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
            'file' => 'required|image|mimes:jpeg,png,jpg,gif,svg,webp|max:1024',
        ];
        $messages = [
            'file.required' => 'Không tìm thấy tập tin hình ảnh',
            'file.image' => 'Hình ảnh không đúng định dạng (JPEG, JPG, PNG, GIF, SVG, WEBP)',
            'file.max' => 'Hình ảnh không được quá :max KB',
        ];

        $validator = Validator::make($request->all(),$rules,$messages);
        return $validator;
    }

    private function deleteByItemId($itemID) {
        $model = new Media();
        $mediable = new Mediable();
        $detail = $model->find($itemID);
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
                    unlink($file);
                }
            }
            $mediable->deleteByWhere($where);
            $mediable->flushCache();
            $model->deleteById($detail->id);
            $model->flushCache();
            return true;
        }
        else {
            return false;
        }
    }
}
