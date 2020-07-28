<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail as MustVerifyEmailContract;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Auth\MustVerifyEmail as MustVerifyEmailTrait;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements MustVerifyEmailContract
{
    use Notifiable,MustVerifyEmailTrait,HasRoles;


    public function topicNotify($instance)
    {
        if($this->id == Auth::id()){
            return false;
        }
        if(method_exists($instance,'toDatabase')){
            $this->increment('notification_count');
        }
        $this->notify($instance);
    }

    /*
     * 清楚未读
     */
    public function markAsRead()
    {
        $this->notification_count = 0;
        $this->save();
        $this->unreadNotifications->markAsRead();
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password','introduction','avatar'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    //关联模型
    public function topics(){
        return $this->hasMany(Topic::class);
    }

    public function replies(){
        return $this->hasMany(Reply::class);
    }

    //权限认证
    public function isAuthorOf($model)
    {
        return $this->id == $model->user_id;
    }
}
