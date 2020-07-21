<?php
namespace App\Handlers;
use Illuminate\Support\Str;

class ImageUploadHandler{
    protected $allow_ext = ['png','jpg','jpeg','gif'];

    public function save($file,$folder,$file_prefix){
        $folder_name = "uploads/images/$folder/".date('Y/md',time());
        $upload_path = public_path().'/'.$folder_name;

        $ext = strtolower($file->getClientOriginalExtension()) ?: 'png';
        $filename = $file_prefix . '_' . time() . '_' . Str::random(10) .'.'. $ext ;
        if(!in_array($ext,$this->allow_ext)){
            return false;
        }

        $file->move($upload_path,$filename);

        return [
            'path'=>config('app.url')."/$folder_name/$filename"
        ];

    }
}
