<?php

namespace App\Observers;

use App\Models\Link;
use Cache;


// creating, created, updating, updated, saving,
// saved,  deleting, deleted, restoring, restored

class LinkObserver
{
    //在保存时 删除缓存
    public function saved(){
        $link = new Link();
        Cache::forget('larabbs_links');
        dd($link);
    }


}
