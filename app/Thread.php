<?php

namespace App;

use App\Filters\ThreadFilters;
use App\Notifications\ThreadWasUpdated;
use Illuminate\Database\Eloquent\Model;

class Thread extends Model
{
    use RecordsActivity;

    protected $guarded = [];

    protected $appends = ['isSubscribedTo'];

    // we want to eagerload this relationship for every single query
    // we can put it in globalScope
    protected $with = ['creator', 'channel'];

    protected static function boot()
    {
        parent::boot();


        static::deleting(function ($thread) {
            $thread->replies->each->delete();
        });


        // static::created(function($thread){
        //    $thread->recordActivity('created');
        // });
    }


    public function path()
    {
        return "/threads/{$this->channel->slug}/{$this->id}";
    }

    public function replies()
    {
        return $this->hasMany(Reply::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function channel()
    {
        return $this->belongsTo(Channel::class, 'channel_id');
    }

    /**
     * Add a reply to the thread.
     *
     * @param  array $reply
     * @return Model
     */
    public function addReply($reply)
    {
        $reply =  $this->replies()->create($reply);

        // Prepare notifications for all subscribers.
        $this->subscriptions
        ->filter(function ($sub) use ($reply) {
            return $sub->user_id != $reply->user_id;
        })
        ->each->notify($reply);
    //     ->each( function ($sub) use ($reply) {
    //         $sub->user->notify(new ThreadWasUpdated($this, $reply));
    // });

        /** Another way to get it */
        
        // foreach ($this->subscriptions as $subscription) {
        //     if ($subscription->user_id != $reply->user_id) {

        //         $subscription->user->notify(new ThreadWasUpdated($this, $reply));
        //     }
        // }

        return $reply;
    }

    public function scopeFilter($query, ThreadFilters $filters)
    {
        return $filters->apply($query);
    }

    /**
     * Subscribe a user to the current thread.
     * 
     * @param int\null $userId
     * @return $this
     */
    public function subscribe($userId = null)
    {
        $this->subscriptions()->create([
            'user_id' => $userId ?: auth()->id()
        ]);

        return $this;
    }

    public function unsubscribe($userId = null)
    {
        $this->subscriptions()
            ->where('user_id', $userId ?: auth()->id())
            ->delete();
    }

    public function subscriptions()
    {
        return $this->hasMany(ThreadSubscription::class);
    }

    public function getIsSubscribedToAttribute()
    {
        return $this->subscriptions()
        ->where('user_id', auth()->id())
        ->exists();
    }
}
