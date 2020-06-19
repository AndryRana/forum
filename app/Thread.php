<?php

namespace App;

use App\Events\ThreadReceivedNewReply;
use App\Filters\ThreadFilters;
use App\Notifications\ThreadWasUpdated;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Str;

class Thread extends Model
{
    use RecordsActivity, RecordsVisits;

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


        static::created(function ($thread) {
           $thread->update(['slug'=> $thread->title]);
        });
    }


    public function path()
    {
        return "/threads/{$this->channel->slug}/{$this->slug}";
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

        event(new ThreadReceivedNewReply($reply));
        // Prepare notifications for all subscribers.
        $this->notifySubscribers($reply);

        return $reply;
    }

    /**
     * @param mixed $reply
     * 
     * @return [type]
     */
    public function notifySubscribers($reply)
    {
        $this->subscriptions
        ->where('user_id', '!=', $reply->user_id)
        ->each
        ->notify($reply);
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

    public function hasUpdatesFor($user)
    {
        //  look in the cache for the proper key.
        // compare that carbon instance with the $thread->updated_at
        // $key = sprintf("users.%s.visits.%s", auth()->id(), $this->id);
       

        $key = $user->visitedThreadCacheKey($this);

        return $this->updated_at > cache($key);

    }

    public function getRouteKeyName()
    {
        return 'slug';
    }

    public function setSlugAttribute($value)
    {
        $slug = Str::slug($value);

        if (static::whereSlug($slug)->exists()) {
            $slug = "{$slug}-{$this->id}";
        }

        $this->attributes['slug'] = $slug;
    }

    public function markBestReply(Reply $reply)
    {
        $reply->thread->update(['best_reply_id' => $reply->id]);
        // $this->best_reply_id = $reply->id;

        // $this->save();
    }

}
