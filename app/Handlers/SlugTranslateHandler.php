<?php
namespace App\Handlers;
use Illuminate\Support\Str;
use GuzzleHttp\Client;
use Overtrue\Pinyin\Pinyin;

class SlugTranslateHandler{

    public function translate($txt){
        return $this->pinyin($txt);
    }

    //汉字转拼音
    public function pinyin($txt){
        return Str::slug(app(Pinyin::class)->permalink($txt));
    }
}
