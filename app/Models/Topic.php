<?php

namespace App\Models;

class Topic extends Model
{
    protected $fillable = ['title', 'body', 'category_id', 'excerpt', 'slug'];

    public function category(){
        return $this->belongsTo(Category::class);
    }

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function replies(){
        return $this->hasMany(Reply::class);
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

    //话题详情 URI
    public function link($params = []){
        return route('topics.show',array_merge([$this->id,$this->slug],$params));
    }

    //更新回复数
    public function updateReplyCount(){
        $this->reply_count = $this->replies->count();
        $this->save();
    }
}
