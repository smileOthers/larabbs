<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
Use Cache;

class Link extends Model
{
    //
    protected $fillable = ['title','link'];
    protected $cache_key = 'larabbs_links';
    protected $cache_expired_in_seconds = 1440*60;

    public function getAllCached(){
        return Cache::remember($this->cache_key,$this->cache_expired_in_seconds,function (){
            return $this->all();
        });
    }
}
