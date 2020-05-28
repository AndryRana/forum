<?php

namespace App\Filters;

use Illuminate\Http\Request;
use App\User;

class ThreadFilters extends Filters
{
    protected $filters = ['by', 'popular' , 'unanswered'];
    // protected $request;
    // protected $builder;

    // public function __construct(Request $request )
    // {
    //     $this->request = $request;
    // }

    // public function apply($builder)
    // {
    //     $this->builder = $builder;
    //     // We apply our filters to th builder
    //     // if(! $username =  $this->request->by) return $builder;
    //     // $user = User::where('name' , $username)->firstOrfail();
    //     // return $builder->where('user_id', $user->id);
    //     if ($this->request->has('by')){
    //         $this->by($this->request->by);
    //     }

    //     return $this->builder;
    // }

    protected function by($username)
    {
        $user = User::where('name' , $username)->firstOrfail();
    
        return $this->builder->where('user_id', $user->id);
    }

    protected function popular()
    {
         $this->builder->getQuery()->orders = [];
        return $this->builder->orderBy('replies_count', 'desc');
    }
    
    protected function unanswered()
    {
        return $this->builder->where('replies_count', 0);
    }
}