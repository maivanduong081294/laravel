<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class Mediable extends BaseModel
{
    use HasFactory;

    protected $fillable = [
        "media_id",
        "type",
        "webp",
    ];

    protected $attributes = [
        "webp" => 0,
    ];

    public $timestamps = false;

    public function getImageSourceById(int $id,string $type='thumbnail') {
        $keyCache = __FUNCTION__.'-'.$id.'-'.$type;
        $value = self::getCache($keyCache);
        if(!self::hasCache($keyCache)) {
            $source = "";
            $media = new Media();
            $detail = $media->getDetailById($id);
            if($detail) {
                $fileName = $detail->file_name;
                $dir = $detail->disk;
                $ext = $detail->mime_type;
                $query = self::selectRaw('mediables.id as resize_id, mediables.type as resize_type, media.*');
                $query = $query->leftJoin('media','media.id', '=', 'mediables.media_id');
                $query = $query->where('media.id',$id)->where('mediables.type',$type);
                $query = $query->orderBy('media.id','asc');
                $sourceDetail = $query->first();
                if($sourceDetail) {
                    if($sourceDetail->resize_type != 'original') {
                        $fileName.= '-'.$sourceDetail->resize_type;
                    }
                    $sourceDir = public_path($dir).'/'.$fileName.'.webp';
                    $source = asset($dir.'/'.$fileName.'.webp');
                    if(!file_exists($sourceDir)) {
                        $sourceDir = public_path($dir).'/'.$fileName.'.'.$ext;
                        $source = asset($dir.'/'.$fileName.'.'.$ext);
                        if(!file_exists($sourceDir)) {
                            $source = asset($dir.'/'.$detail->file_name.'.'.$ext);
                        }
                    }
                }
                else {
                    $source = asset($dir.'/'.$detail->file_name.'.'.$ext);
                }
            }
            $value = $source;
            self::setCache($keyCache,$value);
        }
        return $value;
    }
}
