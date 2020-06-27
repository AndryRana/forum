<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Stevebauman\Purify\Facades\Purify;

class Reply extends Model
{
    use Favoritable, RecordsActivity;

    protected $guarded = [];

    // we want to eagerload this relationship for every single query
    protected $with = ['owner', 'favorites'];
    
    protected $appends = ['favoritesCount', 'isFavorited'];

    protected static function boot()
    {
        parent::boot();

        static::created(function ($reply) {
            $reply->thread->increment('replies_count');
        });
     
        static::deleted(function ($reply) {
            // if ($reply->isBest()) {
            //     $reply->thread->update(['best_reply_id' => null]);
            // }
            $reply->thread->decrement('replies_count');
        });

    }
    
    public function owner()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function thread()
    {
        return $this->belongsTo(Thread::class);
    }
    
    public function wasJustPublished()
    {
        return $this->created_at->gt(Carbon::now()->subMinute());
    }

    public function path()
    {
        return $this->thread->path() . "#reply-{$this->id}";
    }

    public function setBodyAttribute($body)
    {
        $this->attributes['body'] = preg_replace('/@([\w\-]+)/', '<a href="/profiles/$1">$0</a>', $body);
    }

    public function isBest()
    {
        return $this->thread->best_reply_id == $this->id;
    }

    // public function getIsBestAttribute()
    // {
    //     return $this->isBest();
    // }

    public function getBodyAttribute($body)
    {
        return \Purify::clean($body);
    }

}
