<?php

namespace Tests\Feature;

use App\Thread;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReadThreadsTest extends TestCase
{
    use DatabaseMigrations;

    public function setUp(): void
    {
        parent::setUp();

        $this->thread = factory('App\Thread')->create();
    }

    /** @test */
    public function a_user_can_view_all_threads()
    {
        $this->get('/threads')->assertSee($this->thread->title);
    }

    /** @test */
    public function a_user_can_read_a_single_thread()
    {
        $this->get($this->thread->path())->assertSee($this->thread->title);
    }

    /** @test */
    function a_user_can_filter_threads_according_to_a_channel()
    {
        $channel = create('App\Channel');
        $threadInChannel = create('App\Thread', ['channel_id' => $channel->id] );
        $threadNotInChannel = create('App\Thread');
        
        $this->get('/threads/' . $channel->slug)
        ->assertSee($threadInChannel->title)
        ->assertDontSee($threadNotInChannel->title);
    }   
    
    /** @test */
    function a_user_can_filter_threads_by_any_username()
    {
        $this->signIn(create('App\User', ['name' =>'JohnDoe']));
        
        $threadByJohn = create('App\Thread', ['user_id' => auth()->id()]);
        $threadNotByJohn = create('App\Thread');

        $this->get('threads?by=JohnDoe')
        ->assertSee($threadByJohn->title)
        ->assertDontSee($threadNotByJohn->title);
    }

    /** @test */
    function a_user_can_filter_threads_by_popularity()
    {
        // Given we have three threads
        // with 2 replies, 3 replies, and 0 replies, respectively.
        $threadWithTwoReplies = create('App\Thread');
        create('App\Reply', ['thread_id' =>$threadWithTwoReplies->id], 2);
       
        $threadWithThreeReplies = create('App\Thread');
        create('App\Reply', ['thread_id' =>$threadWithThreeReplies->id], 3);
        
        $threadWithNoReplies = create('App\Thread');

        // When I filter all threads by popularity
        $response = $this->getJson('/threads?popular=1')->json();
        // then they should be returned from most replies to least 
        $this->assertEquals([3,2,0,0], array_column($response['data'], 'replies_count'));
    }
    
    /** @test */
    public function a_user_can_filter_by_those_that_are_unanswered()
    {
        $thread = create('App\Thread');
        create('App\Reply', ['thread_id' => $thread->id]);
        
        $response = $this->getJson('threads?unanswered=1')->json();

        $this->assertCount(1, $response['data']);

    }    
    /** @test */
    public function a_user_can_request_all_replies_for_given_thread()
    {
        $thread = create('App\Thread');
        create('App\Reply', ['thread_id' => $thread->id] , 2);

        $response = $this->getJson($thread->path() . '/replies')->json();
        // dd($response);
        $this->assertCount(2, $response['data']);
        $this->assertEquals(2, $response['total']);

    }
}