<?php
namespace app\Models\Traits;
use Illuminate\Support\Facades\Redis;
use Carbon\Carbon;

trait LastActivedAtHelper{
    protected $hash_prefix = 'larabbs_last_actives_at_';
    protected $field_prefix = 'user_';

    //把用户最后操作时间写入Redis
    public function recordLastActivedAt(){
        //hash表的名称
        $hash = $this->getHashFromDateString(Carbon::now()->toDateString());

        //字段名称
        $field = $this->getHashField();
        //当前时间
        $now = Carbon::now()->toDateTimeString();

        //数据写入Redis 已经存在会被更新
        Redis::hSet($hash,$field,$now);
    }

    //把Redis中数据同步到MySQL
    public function syncUserActivedAt(){
        //hash表的名称
        $hash = $this->getHashFromDateString(Carbon::yesterday()->toDateString());

        $dates = Redis::hGetAll($hash);
        foreach ($dates as $k=>$v){
            $user_id = str_replace($this->field_prefix,'',$k);
            if($user = $this->find($user_id)){
                $user->last_actived_at = $v;
                $user->save();
            }
        }

        //删除
        Redis::del($hash);
    }

    /*
     * 字段访问器
     * 访问该字段时 最后获取到的是经过该函数的返回值
     */
    public function getLastActivedAtAttribute($value){

        $hash = $this->getHashFromDateString(Carbon::now()->toDateString());
        $field = $this->getHashField();
        $datetime  = Redis::hGet($hash,$field) ? : $value;
        if($datetime){
            return new Carbon($datetime);
        }else{
            return $this->created_at;
        }
    }

    public function getHashFromDateString($date){
        return $this->hash_prefix . $date;
    }

    public function getHashField(){
        return $this->field_prefix . $this->id;
    }
}
