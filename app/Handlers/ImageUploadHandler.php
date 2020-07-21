<?php
namespace App\Handlers;
use Illuminate\Support\Str;
use Image;

class ImageUploadHandler{
    protected $allow_ext = ['png','jpg','jpeg','gif'];

    public function save($file,$folder,$file_prefix,$max_width = false){
        $folder_name = "uploads/images/$folder/".date('Y/md',time());
        $upload_path = public_path().'/'.$folder_name;

        $ext = strtolower($file->getClientOriginalExtension()) ?: 'png';
        $filename = $file_prefix . '_' . time() . '_' . Str::random(10) .'.'. $ext ;
        if(!in_array($ext,$this->allow_ext)){
            return false;
        }

        $file->move($upload_path,$filename);

        if($max_width && $ext != 'gif'){
            $this->reduceSize($upload_path.'/'.$filename,$max_width);
        }

        return [
            'path'=>config('app.url')."/$folder_name/$filename"
        ];
    }

    /*
     * 剪切图片
     */
    public function reduceSize($file_path,$max_width){
        $image = Image::make($file_path);
        // 进行大小调整的操作
        $image->resize($max_width, null, function ($constraint) {

            // 设定宽度是 $max_width，高度等比例缩放
            $constraint->aspectRatio();

            // 防止裁图时图片尺寸变大
            $constraint->upsize();
        });

        // 对图片修改后进行保存
        $image->save();
    }
}
