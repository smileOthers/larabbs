<?php

namespace App\Models;

class Topic extends Model
{
    protected $fillable = ['title', 'body', 'user_id', 'category_id', 'reply_count', 'view_count', 'last_reply_user_id', 'order', 'excerpt', 'slug'];

    public function category(){
        return $this->belongsTo(Category::class);
    }

    public function user(){
        return $this->belongsTo(User::class);
    }

    //排序方式选择
    public function scopeWithOrder($query,$order)
    {
        switch ($order){
            case 'recent':
                $query->recent();
                break;
            default:
                $query->recentReplied();
                break;
        }
    }

    //按最新回复排序
    public function scopeRecentReplied($query){
        return $query->orderBy('updated_at','desc');
    }

    //按发布时间排序
    public function scopeRecent($query)
    {
        return $query->orderBy('created_at','desc');
    }
}
