<?php

namespace App\Http\Controllers;

use App\Favorite;
use App\Reply;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

class FavoritesController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    
    public function store(Reply $reply)
    {
        $reply->favorite();

        return back();

        
        // this create  the  'favorited_id' => $reply->id, 
        // and 'favorited_type' => get_class($reply)
        // BECAUSE of the POLYMORPHISM RELATION
        // $reply->favorites()->create(['user_id' =>auth()->id()]);
    
    
    //    Favorite::create([
    //         'user_id' => auth()->id(),
    //         'favorited_id' => $reply->id,
    //         'favorited_type' => get_class($reply)
        // ]);
    }

    public function destroy(Reply $reply)
    {
        $reply->unfavorite();
    }
}
