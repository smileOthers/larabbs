<?php

use Illuminate\Database\Seeder;
use App\Models\Topic;
use App\Models\User;
use App\Models\Reply;

class RepliesTableSeeder extends Seeder
{
    public function run()
    {
        //所有用户ID
        $user_ids = User::all()->pluck('id')->toArray();
        //所有栏目
        $topic_ids = Topic::all()->pluck('id')->toArray();
        $faker = app(\Faker\Generator::class);

        $topics = factory(Reply::class)
            ->times(1000)
            ->make()
            ->each(function ($topic, $index) use ($user_ids,$topic_ids,$faker) {
                $topic->user_id = $faker->randomElement($user_ids);
                $topic->topic_id = $faker->randomElement($topic_ids);

        });
        Reply::insert($topics->toArray());
    }

}

