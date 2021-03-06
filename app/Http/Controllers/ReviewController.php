<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Review;
use App\User;
use Illuminate\Support\Facades\DB;

class ReviewController extends Controller
{
    public function index(Request $request){
      //プルダウン用のデータを取得
      $groupnames =Review::select('group_name')->GROUPBY('group_name')->get();
     
     //ユーザー名を取得するために結合 
    $test= DB::table('reviews')->where('reviews.user_id',2)->join('users','users.id','=','reviews.user_id')->get();
    
    // \Debugbar::info('$test3='.$test2); 

     //検索データ取得
    if(isset($request->group) && $request->group !="グループ名で検索"){
      $keyword=$request->group;
    }else{
        $keyword=$request->search;
    }
      
    if(isset($keyword)){
        $reviews=Review::join('users','users.id','=','reviews.user_id')->where(
            function($query) use($keyword){$query->where('title', $keyword)->orwhere('group_name',$keyword);})
            ->orderBy('reviews.created_at', 'DESC')->paginate(6);
        }else{
        $reviews =Review::join('users','users.id','=','reviews.user_id')->where('status', 1)->orderBy('reviews.created_at', 'DESC')
        ->select('users.id as u_id','reviews.id','user_id','title','group_name','body','image','status' ,'reviews.created_at' ,'name')
        ->paginate(6);
        $review_user=$reviews->pluck('user_id');
        }
        
    \Debugbar::info($reviews);
        return view('index', compact('reviews','keyword','groupnames'));
    }

    
    public function create(){
        return view('review');
        }

      public function store(Request $request)
    {
        
        $post = $request->all();
        
         $validatedData = $request->validate([
        'title' => 'required|max:255',
        'body' => 'required',
        'image' => 'mimes:jpeg,png,jpg,gif,svg|max:2048',
    ]);
        if($request->hasfile('image')){
         $request->file('image')->store('/public/images');
         $data = ['user_id' => \Auth::id(), 'title' => $post['title'],'group_name' => $post['group'],'body' => $post['body'], 'image' => $request->file('image')->hashName()];
        }else{
            $data = ['user_id' => \Auth::id(), 'title' => $post['title'],'group_name' => $post['group'], 'body' => $post['body']];
        }
        
        Review::insert($data);
        return redirect('/')->with('flash_message', '投稿が完了しました');
    }
   public function show($id)
{
    $review = Review::where('id', $id)->where('status', 1)->first();
    return view('show', compact('review'));
 
}

public function edit($id){
         $review = Review::where('id', $id)->where('status', 1)->first();
         return view('edit', compact('review'));
}

public function update(Request $request){
         $post = $request->all();
         $validatedData = $request->validate([
        'title' => 'required|max:255',
        'body' => 'required',
        'image' => 'mimes:jpeg,png,jpg,gif,svg|max:2048',
    ]);
        if($request->hasfile('image')){
         $request->file('image')->store('/public/images');
         $data = ['user_id' => \Auth::id(), 'title' => $post['title'],'group_name' => $post['group'], 'body' => $post['body'],'image' => $request->file('image')->hashName()];
        }else{
            $data = ['user_id' => \Auth::id(), 'title' => $post['title'],'group_name' => $post['group'], 'body' => $post['body']];
        }Review::where('id',$request->id)->update($data);
        return redirect('/')->with('flash_message', '編集が完了しました');
    }

public function delete($id){
    Review::where('id',$id)->where('status',1)->delete();
    return redirect('/')->with('flash_message','削除が完了しました');
}

}

