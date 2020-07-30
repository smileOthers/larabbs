<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Link;
use App\Models\Topic;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\TopicRequest;
use Illuminate\Support\Facades\Auth;
use App\Handlers\ImageUploadHandler;

class TopicsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth', ['except' => ['index', 'show']]);
    }

    //话题广场
	public function index(Request $request,Topic $topic,User $user,Link $link)
	{

		$topics = $topic->withOrder($request->order)->with('user','category')->paginate(20);

		$active_users = $user->getActiveUsers();

        $links = $link->getAllCached();

		return view('topics.index', compact('topics','active_users','links'));
	}

	//话题详情
    public function show(Request $request,Topic $topic)
    {
        if ( ! empty($topic->slug) && $topic->slug != $request->slug) {
            return redirect($topic->link(), 301);
        }
        return view('topics.show', compact('topic'));
    }

    //新建话题 页面
	public function create(Topic $topic)
	{
	    $categories = Category::all();
		return view('topics.create_and_edit', compact('topic','categories'));
	}

	public function store(TopicRequest $request,Topic $topic)
	{
        $topic->fill($request->all());
        $topic->user_id = Auth::id();
        $topic->save();
		return redirect()->to($topic->link())->with('message', 'Created successfully.');
	}

	//话题修改页面
	public function edit(Topic $topic)
	{
        $this->authorize('update', $topic);
        $categories = Category::all();
		return view('topics.create_and_edit', compact('topic','categories'));
	}

	//话题修改 逻辑
	public function update(TopicRequest $request, Topic $topic)
	{
		$this->authorize('update', $topic);
		$topic->update($request->all());

		return redirect()->to($topic->link())->with('message', '修改成功');
	}

	//话题删除
	public function destroy(Topic $topic)
	{
		$this->authorize('destroy', $topic);
		$topic->delete();

		return redirect()->route('topics.index')->with('message', '删除成功');
	}

	//图片上传
    public function uploadImage(Request $request,ImageUploadHandler $uploader){
        // 初始化返回数据，默认是失败的
        $data = [
            'success'   => false,
            'msg'       => '上传失败!',
            'file_path' => ''
        ];
        if($file = $request->upload_file){
            $request = $uploader->save($file,'topics',Auth::id(),1024);
            if($request){
                $data = [
                    'success'   => true,
                    'msg'       => '上传成功',
                    'file_path' => $request['path']
                ];
            }
        }
        return $data;
    }
}
