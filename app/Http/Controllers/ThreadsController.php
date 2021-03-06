<?php

namespace App\Http\Controllers;

use App\Channel;
use App\Filters\ThreadFilters as ThreadFilters;
use App\Rules\Recaptcha;
use App\Thread;
use App\Trending;
use Illuminate\Http\Request;

class ThreadsController extends Controller
{
    
   public function __construct()
   {
       $this->middleware('auth')->except(['index','show']);
   }
   
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Channel $channel,ThreadFilters $filters, Trending $trending)
    {
        // if ($channel->exists) {
        //     $threads = $channel->threads()->latest();
        // } else {
        //     $threads = Thread::latest();
        // }
        
     //    if request('by'), we should filter by the given username
    //  if($username = request('by')) {
    //      $user = User::where('name' , $username)->firstOrfail();
 
    //      $threads->where('user_id', $user->id);
    //  }
         $threads = $this->getThreads($channel, $filters);

         if (request()->wantsJson()) {
             return $threads;
         }

      
        // $threads = $this->getThreads($channel);

        return view('threads.index',[
            'threads' => $threads,
            'trending' => $trending->get()
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('threads.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Recaptcha $recaptcha)
    {
        
        request()->validate([
            'title' => 'required|spamfree',
            'body' => 'required|spamfree',
            'channel_id' => 'required|exists:channels,id',
            'g-recaptcha-response' => [$recaptcha]
        ]);

      
        
        $thread = Thread::create([
            'user_id' => auth()->id(),
            'channel_id' => request('channel_id'),
            'title' => request('title'),
            'body' => request('body'),
            'slug' => request('title')
        ]);

        if (request()->wantsJson()) {
            return response($thread, 201);
        }

        return  redirect($thread->path())
        ->with('flash', 'Your thread has been published');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Thread  $thread
     * @return \Illuminate\Http\Response
     */
    public function show($channelId, Thread $thread, Trending $trending)
    {
       
        // return $thread->load('replies');
        // return Thread::withCount('replies')->find(51);
        // return $thread->replyCount;
        //  return $thread;  it gives replies_count: 1

        // Record that the user visited this page
        //  Record a Timestamp
        // $key = sprintf("users.%s.visits.%s", auth()->id(), $thread->id);

        // cache()->forever($key, Carbon::now());

        if (auth()->check()) {
            auth()->user()->read($thread);
        }

        $trending->push($thread);

        $thread->recordVisit();
        
        
        return view('threads.show', compact('thread'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Thread  $thread
     * @return \Illuminate\Http\Response
     */
    public function edit(Thread $thread)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Thread  $thread
     * @return \Illuminate\Http\Response
     */
    public function update($channel, Thread $thread)
    {
        // Authorization
        $this->authorize('update', $thread);
        // validation amd update
        $thread->update(request()->validate([
            'title' => 'required|spamfree',
            'body' => 'required|spamfree',
        ]));

        return $thread;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Thread  $thread
     * @return \Illuminate\Http\Response
     */
    public function destroy($channel, Thread $thread)
    {
        $this->authorize('update', $thread);

        // $thread->replies()->delete();
        $thread->delete();
        
        if (request()->wantsJson()) {
            return response([], 204);
        }
        return redirect('/threads');
    }
    
    // protected function getThreads(Channel $channel)
    // {
    //     if ($channel->exists) {
    //         $threads = $channel->threads()->latest();
    //     } else {
    //         $threads = Thread::latest();
    //     }
        
    //  //    if request('by'), we should filter by the given username
    //  if($username = request('by')) {
    //      $user = User::where('name' , $username)->firstOrfail();
 
    //      $threads->where('user_id', $user->id);
    //  }
    //      $threads = $threads->get();
    //      return $threads;
    // }
    protected function getThreads(Channel $channel, ThreadFilters $filters)
    {
        $threads = Thread::latest()->filter($filters);

        if ($channel->exists) {
            $threads->where('channel_id', $channel->id);
        }
        // dd($threads->toSql());
        return $threads->paginate(5);
    }
}
