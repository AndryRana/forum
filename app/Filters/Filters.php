<?php

namespace App\Filters;

use Illuminate\Http\Request;

abstract class Filters
{
    protected $request, $builder;
    protected $filters = [];

    public function __construct(Request $request )
    {
        $this->request = $request;
    }

    public function apply($builder)
    {
        // We apply our filters to th builder
        // if(! $username =  $this->request->by) return $builder;
        // $user = User::where('name' , $username)->firstOrfail();
        // return $builder->where('user_id', $user->id);
        
        $this->builder = $builder;
        // if ($this->request->has('by')){
        //     $this->by($this->request->by);
        // }
        foreach ($this->getFilters() as $filter => $value) {
            if (method_exists($this, $filter)){
                $this->$filter($value);
            }

        }
        return $this->builder;
    }   

    public function getFilters()
    {
        //  $filters = array_intersect(array_keys($this->request->all()), $this->filters);

        // return $this->request->only($filters);    
        return array_filter($this->request->only($this->filters));
    }

}