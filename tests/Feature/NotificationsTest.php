<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Notifications\DatabaseNotification;
use Tests\TestCase;

class NotificationsTest extends TestCase
{
    use DatabaseMigrations;

    // public function setUp()
    // {
    //     parent::setUp();

    //     $this->signIn();
    // }

    /** @test */
    public function a_notification_is_prepared_when_a_subscribed_thread_receives_a_new_reply_that_is_not_by_the_current_user()
    {
        $this->signIn();

        $thread = create('App\Thread')->subscribe();
        
        $this->assertCount(0, auth()->user()->notifications);
       
        //  Then, each time a new reply is left...
        $thread->addReply([
            'user_id' => auth()->id(),
            'body' => 'Some Reply'
        ]);
        
        //  A notification should be prepared for the user.
        $this->assertCount(0, auth()->user()->fresh()->notifications);
       
        $thread->addReply([
            'user_id' => create('App\User')->id,
            'body' => 'Somebody else Reply'
        ]);
        
        $this->assertCount(1, auth()->user()->fresh()->notifications);

    }


    /** @test */
    public function a_user_can_fetch_their_unread_notifications()
    {
        $this->signIn();
        create(DatabaseNotification::class);
        // $thread = create('App\Thread')->subscribe();
        
        // $thread->addReply([
            //     'user_id' => create('App\User')->id,
            //     'body' => 'Some Reply here'
            //     ]);
            
            $user = auth()->user();
            
            $this->assertCount(
                1, 
                $this->getJson("/profiles/". auth()->user()->name . "/notifications/")->json()
            );
        }
        
        
        /** @test */
        public function a_user_can_mark_a_notification_as_read()
        {
            $this->signIn();
        create(DatabaseNotification::class);
        // $thread = create('App\Thread')->subscribe();
        
        // $thread->addReply([
        //     'user_id' => create('App\User')->id,
        //     'body' => 'Some Reply here'
        // ]);

        tap(auth()->user(), function ($user){
            $this->assertCount(1, $user->unreadNotifications);
    
            $this->delete("/profiles/{$user->name}/notifications/" . $user->unreadNotifications->first()->id);
            
            $this->assertCount(0, $user->fresh()->unreadNotifications);
        });

    }
}
