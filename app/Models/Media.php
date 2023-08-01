<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class Media extends BaseModel
{
    use HasFactory;

    protected $fillable = [
        "name",
        "file_name",
        "disk",
        "mime_type",
        "type",
        "author_id",
    ];

    public function getImageSourceById(int $id,string $type='thumbnail') {
        $mediable = new Mediable();
        return $mediable->getImageSourceById($id, $type);
    }

    public function getListUser() {
        $keyCache = __FUNCTION__;
        $value = self::getCache($keyCache);
        if(!self::hasCache($keyCache)) {
            $authorIds = self::selectRaw('distinct(author_id)');
            $user = new User();
            $result = $user->whereIn('id',$authorIds)->get();
            $data = [];
            if($result->count() > 0) {
                foreach($result as $user) {
                    $data[$user->id] = $user;
                }
            }
            $value = $data;
            self::setCache($keyCache,$value);
        }
        return $value;
    }
}
