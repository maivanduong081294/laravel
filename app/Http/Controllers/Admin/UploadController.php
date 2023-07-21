<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Http\Controllers\Admin\Controller;

use Validator;
use Storage;
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

    public function index() {
        $this->heading = 'Thư viện hình ảnh';
        return $this->view('index');
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

    public function convertWebp() {
        \Tinify\setKey("ZtSzTNCxDtVBNBRrmfyVQrvgWLZTBHny");
        
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
