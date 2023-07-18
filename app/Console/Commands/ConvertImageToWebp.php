<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use DB;
class ConvertImageToWebp extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'convertimagetowebp:cron';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Convert Image To WEBP by Tiny PNG';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $query = DB::table('mediables')->select(DB::raw('mediables.id as resize_id, mediables.type as resize_type, media.*'));
        $query = $query->leftJoin('media','media.id', '=', 'mediables.media_id');
        $query = $query->where('webp',0);
        $query = $query->orderBy('media.id','asc');
        $detail = $query->first();
        $msg = '';
        if($detail) {
            try {
                $apiKey = "ZtSzTNCxDtVBNBRrmfyVQrvgWLZTBHny";
                \Tinify\setKey($apiKey);
                \Tinify\validate();
                $compression = \Tinify\compressionCount();
                $limit = 1000;
                if($compression < $limit) {
                    $fileName = $detail->file_name;
                    $dir = $detail->disk;
                    $ext = $detail->mime_type;
                    if($detail->resize_type != 'original') {
                        $fileName.= '-'.$detail->resize_type;
                    }
                    $file_dir = public_path($dir).'/'.$fileName.'.'.$ext;
                    if(file_exists($file_dir)) {
                        $source = \Tinify\fromFile($file_dir);
                        $converted = $source->convert(array("type" => ["image/webp","image/png"]));
                        $extension = $converted->result()->extension();
                        $check = $source->toFile(public_path($dir).'/'.$fileName.'.'.$extension);
                        if($check) {
                            $msg = 'Success';
                            DB::table('mediables')->where('id',$detail->resize_id)->update(['webp'=>1,'webp_msg' => $msg]);
                        }
                    }
                    else {
                        $msg = 'File Not Found';
                        DB::table('mediables')->where('id',$detail->resize_id)->update(['webp'=>1,'webp_msg' => $msg]);
                    }
                }
            } catch(\Tinify\Exception $e) {
                $msg = $e->getMessage();
                DB::table('mediables')->where('id',$detail->resize_id)->update(['webp'=>2,'webp_msg' => $msg]);
            }
        }
        else {
            $msg = 'Empty';
        }
        \Log::info("Convert Image To Webp Cron is working fine!: {$msg}");
        return 0;
    }
}
