<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class SubscribeToThreadTest extends TestCase
{
   use DatabaseMigrations;

   /** @test */

    public function a_user_can_subscribe_to_threads()
    {
        $this->signIn();

        // Given we have a thread...
        $thread = create('App\Thread');

        // And the user subscribes to the thread...
        $this->post($thread->path() . '/subscriptions');

        //  Then, each time a new reply is left...
        $thread->addReply([
            'user_id' => auth()->id(),
            'body' => 'Some Reply'
        ]);

        //  A notification should be prepared for the user.
        $this->assertCount(1, $thread->subscriptions);
    }
    
    /** @test */
    public function a_user_can_unsubscribe_from_threads()
    {
        $this->signIn();
        
        // Given we have a thread...
        $thread = create('App\Thread');
        
        $thread->subscribe();
        
        // And the user subscribes to the thread...
        $this->delete($thread->path() . '/subscriptions');
        
        $this->assertCount(0, $thread->subscriptions);

    }
}
