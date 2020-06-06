<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreatePostRequest;
use App\Reply;
use App\Thread;
use Illuminate\Support\Facades\Gate;

class RepliesController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth', ['except' => 'index']);
    }



    public function index($channelId, Thread $thread)
    {
        return $thread->replies()->paginate(5);
    }




    public function store($channelId, Thread $thread, CreatePostRequest $form)
    {
        
        // if (Gate::denies('create', new Reply)) {
        //     return response(
        //         'You are posting too frequently.Please take a break. :)', 429);

        // }
            // $this->authorize('create', new Reply);
            // $this->validate(request(), ['body' => 'required|spamfree']);

           return $thread->addReply([
                'body' => request('body'),
                'user_id' => auth()->id()
            ])->load('owner');

    }




    public function update(Reply $reply)
    {
        $this->authorize('update', $reply);

        try {

            request()->validate(['body' => 'required|spamfree']);
            // $this->validate(request(), ['body' => 'required|spamfree']);
    
            $reply->update(request(['body']));
        } catch (\Exception $e) {
            return response('Sorry, your reply could not be saved at this time.', 422);
         }
    }




    public function destroy(Reply $reply)
    {
       $this->authorize('update', $reply);
        
        $reply->delete();

        if (request()->expectsJson()) {
            return response(['status' => 'Reply deleted']);
        }

        return back();
    }

}
