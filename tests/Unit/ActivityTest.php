<?php

namespace Tests\Unit;

use App\Activity;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class ActivityTest extends TestCase
{
   use DatabaseMigrations;

   /** @test */
   public function it_records_activity_when_thread_is_created()
    {
        $this->signIn();

        $thread = create('App\Thread');

        $this->assertDatabaseHas('activities', [
            'type' => 'created_thread',
            'user_id' => auth()->id(),
            'subject_id' => $thread->id,
            'subject_type' => 'App\Thread'
        ]);

        $activity = Activity::first();

        $this->assertEquals($activity->subject->id, $thread->id);
    }

    /** @test */
    public function it_records_activity_when_a_reply_is_created()
    {
       $this->signIn();

       create('App\Reply');

       $this->assertEquals(2, Activity::count());

    }

    /** @test */
    public function it_fecthes_a_feed_for_any_user()
    {
        // given we have thread
        $this->signIn();

        create('App\Thread', ['user_id' => auth()->id()], 2);

        // And another thread from a week ago
        auth()->user()->activity()->first()->update(['created_at' => Carbon::now()->subWeek()]);

        // when we fetch their thread
        $feed = Activity::feed(auth()->user());
        // Then, it should be returned in the proper format.
        $this->assertTrue($feed->keys()->contains(
            Carbon::now()->format('Y-m-d')
        ));
       
        $this->assertTrue($feed->keys()->contains(
            Carbon::now()->subWeek()->format('Y-m-d')
        ));
    }
}
