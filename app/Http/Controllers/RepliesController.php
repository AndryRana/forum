<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreatePostRequest;
use App\Notifications\YouWereMentioned;
use App\Reply;
use App\Thread;
use App\User;
use Illuminate\Support\Facades\Gate;

class RepliesController extends Controller
{
    
    public function __construct()
    {
        $this->middleware('auth', ['except' => 'index']);
    }



    /**
     * Undocumented function
     *
     * @param [Integer] $channelId
     * @param Thread $thread
     * @return void
     */
    public function index($channelId, Thread $thread)
    {
        return $thread->replies()->paginate(5);
    }


    /**
     * @param mixed $channelId
     * @param Thread $thread
     * @param CreatePostRequest $form
     * 
     * @return [type]
     */
    public function store($channelId, Thread $thread, CreatePostRequest $form)
    {
        
        // if (Gate::denies('create', new Reply)) {
        //     return response(
        //         'You are posting too frequently.Please take a break. :)', 429);

        // }
            // $this->authorize('create', new Reply);
            // $this->validate(request(), ['body' => 'required|spamfree']);

            return  $thread->addReply([
                'body' => request('body'),
                'user_id' => auth()->id()
            ])->load('owner');
            
            // // Inspect the body of the reply for username mentions
            // preg_match_all('/\@([^\s\.]+)/', $reply->body, $matches);
            // // dd($matches);
            // $names = $matches[1];
            
            // // And then for each mentionned user , notify them.
            // foreach ($names as $name) {
            //     $user = User::whereName($name)->first();

            //     if ($user) {
            //         $user->notify(new YouWereMentioned($reply));
            //     }
            // }

    }


    /**
     * @param Reply $reply
     * 
     * @return [type]
     */
    public function update(Reply $reply)
    {
        $this->authorize('update', $reply);

            request()->validate(['body' => 'required|spamfree']);
            // $this->validate(request(), ['body' => 'required|spamfree']);
    
            $reply->update(request(['body']));

    }


    /**
     * Delete Reply
     * 
     * @param Reply $reply
     * 
     * @return [type]
     */
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
