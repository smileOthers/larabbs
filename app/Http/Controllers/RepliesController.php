<?php

namespace App\Http\Controllers;
use App\Http\Requests\ReplyRequest;
use App\Models\Reply;
use Illuminate\Support\Facades\Auth;

class RepliesController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /*
     * 评论保存
     */
    public function store(ReplyRequest $replyRequest, Reply $reply){
        $reply->content = $replyRequest->content;
        $reply->user_id = Auth::id();
        $reply->topic_id = $replyRequest->topic_id;
        $reply->save();

        return redirect()->to($reply->topic->link())->with('success','评论成功');
    }

    //删除回复
    public function destroy(Reply $reply){
        $this->authorize('destroy', $reply);
        $reply->delete();
        return redirect()->to($reply->topic->link())->with('success','评论删除成功');
    }
}
