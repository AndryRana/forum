<?php

namespace App\Http\Controllers;

use App\Thread;
use App\Trending;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function show(Trending $trending)
    {
        $q = request()->input('q');
        $threads = Thread::where('title', 'like', "%$q%" )
                ->orWhere('body', 'like', "%$q%" )
                ->paginate(10);
                
        // $threads = Thread::search(request('q'))->paginate(25);

        // if (request()->expectsJson()) {
        //     return $threads;
        // }

        return view('threads.index', [
            'threads' => $threads,
            'trending' => $trending->get()
        ]);

        // return view('threads.search', [
        //     'trending' => $trending->get()
        // ]);
    }
}
