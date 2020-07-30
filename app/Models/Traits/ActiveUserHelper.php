<?php
namespace app\Models\Traits;

use App\Models\Topic;
use App\Models\Reply;
use Carbon\Carbon;
use Cache;
use DB;
use Arr;

trait ActiveUserHelper{
    //用于存放临时用户数据
    protected $user = [];
    //配置信息
    protected $topic_weight = 4;
    protected $reply_weight = 1;
    protected $pass_day = 7;
    protected $user_number = 6;

    //缓存相关配置
    protected $cache_key = 'larabbs_active_users';
    protected $cache_expire_in_seconds = 65*60;

    //获取活跃用户 前台页面使用
    public function getActiveUsers(){
        return Cache::remember($this->cache_key,$this->cache_expire_in_seconds,function (){
            return $this->calculateActiveUsers();
        });
    }

    //取出活跃用户表并且缓存
    public function calculateAndCacheActiveUsers(){
        // 取得活跃用户列表
        $active_users = $this->calculateActiveUsers();
        // 并加以缓存
        $this->cacheActiveUsers($active_users);
    }

    //根据算法计算活跃用户
    private function calculateActiveUsers(){
        $this->calculateTopicScore();
        $this->calculateReplyScore();

        //数组按照得分排序
        $users = Arr::sort($this->user,function ($user){
            return $user['score'];
        });

        //数组取反
        $users = array_reverse($users,true);
        //数组截取
        $users = array_slice($users,0,$this->user_number,true);

        //新建一个空集合
        $active_users = collect();
        foreach ($users as $user_id=>$user){
            //尝试是否可以找到用户
            $user = $this->find($user_id);
            if($user){
                $active_users->push($user);
            }
        }
        return $active_users;
    }

    //计算用户发布话题所得活跃分
    private function calculateTopicScore(){
        $topic_users = Topic::query()
            ->select(DB::raw('user_id,count(*) as topic_count'))
            ->where('created_at','>=',Carbon::now()->subDay($this->pass_day))
            ->groupBy('user_id')
            ->get();
        foreach ($topic_users as $v){
            $this->user[$v->user_id]['score'] = $v->topic_count * $this->topic_weight;
        }
    }

    //计算用户发布回复所得活跃分
    private function calculateReplyScore(){
        $reply_users = Reply::query()
            ->select(DB::raw('user_id,count(*) as reply_count'))
            ->where('created_at','>=',Carbon::now()->subDay($this->pass_day))
            ->groupBy('user_id')
            ->get();
        foreach ($reply_users as $v){
            $reply_score = $v->reply_count * $this->reply_weight;
            if(isset($this->user[$v->user_id])){
                $this->user[$v->user_id]['score'] += $reply_score;
            }else{
                $this->user[$v->user_id]['score'] = $reply_score;
            }
        }
    }

    //把数据写入缓存
    private function cacheActiveUsers($active_users){
        Cache::put($this->cache_key,$active_users,$this->cache_expire_in_seconds);
    }


}
