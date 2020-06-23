<?php

namespace Tests\Feature;

use App\Activity;
use App\Rules\Recaptcha;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class CreateThreadsTest extends TestCase
{
    use DatabaseMigrations;

    public function setUp(): void
    {
        parent::setUp();

        app()->singleton(Recaptcha::class, function () {
            return \Mockery::mock(Recaptcha::class, function ($m){
                $m->shouldReceive('passes')->andReturn(true);
            });

        });

        $this->user = factory('App\User')->create();
    }

   /** @test */
   function guests_may_not_create_a_threads()
   {
       // We should expect an authenticated error exception
       $this->expectException('Illuminate\Auth\AuthenticationException');
       $this->withoutExceptionHandling();

       $this->get('/threads/create')
       ->assertRedirect('/login');

       $this->post(route('threads'))
       ->assertRedirect('/login');
       // Given we have a thread
    //    $thread =make('App\Thread');

       // And a guest posts a new thread to the endpoint
    //    $this->post('/threads', $thread->toArray());


   }


   /** @test */
   public function new_users_must_first_confirm_their_email_address_before_creating_threads()
   {
    $user = factory('App\User')->states('unconfirmed')->create();

    $this->signIn($user);

    $thread = make('App\Thread');


    $this->post(route('threads'), $thread->toArray())
       ->assertRedirect('/threads')
       ->assertSessionHas('flash', 'You must confirm your email address.');
   }


   /** @test */
   function guests_cannot_see_the_create_thread_page()
       {
            $this->get('/threads/create')
            ->assertRedirect('/login');
       }
   
   
    /** @test */
    function a_user_can_create_new_forum_threads()
    {
         //$this->withoutExceptionHandling();

        // Given we have a user
        // $user = create('App\User');
        $user = $this->signIn();
        // And that user is authenticated
        // $this->actingAs($user);

        // And we have a thread created by that user
        $thread = make('App\Thread');
        // $thread = factory('App\Thread')->create([
        //     'user_id' => $user->id
        // ]);
        
        // And once we hit the endpoint to create a new thread
        
        $response = $this->post(route('threads'), $thread->toArray() + ['g-recaptcha-response' => 'token']);
        // And when we visit the thread page
        // Then we should see the new thread's title and body
        $this->get($response->headers->get('location'))->assertSee($thread->title)->assertSee($thread->body);
    }

    /** @test */
    function a_thread_requires_a_title()
    {
        $this->publishThread(['title' => null])
        ->assertSessionHasErrors('title');
       
    }

    
    /** @test */
    function a_thread_requires_a_body()
    {
        $this->publishThread(['body' => null])
        ->assertSessionHasErrors('body');
    }
    
    /** @test */
    public function a_thread_requires_recaptcha_verification()
    {
        unset(app()[Recaptcha::class]);

        $this->publishThread(['g-recaptcha-response' => 'test'])
        ->assertSessionHasErrors('g-recaptcha-response');
    }


    /** @test */
    function a_thread_requires_a_valid_channel()
    {
        factory('App\Channel',2)->create();

        $this->publishThread(['channel_id' => null])
        ->assertSessionHasErrors('channel_id');
        
        $this->publishThread(['channel_id' => 999])
        ->assertSessionHasErrors('channel_id');
    }
    
    /** @test */
    public function a_thread_requires_a_unique_slug()
    {
        $this->signIn();

        $thread = create('App\Thread', ['title' => 'Foo Title']);
        
        $this->assertEquals($thread->fresh()->slug, 'foo-title');
        
        $thread = $this->postJson(route('threads'), $thread->toArray() + ['g-recaptcha-response' => 'token'])->json();

        $this->assertEquals("foo-title-{$thread['id']}", $thread['slug']);
        
    }
    
    /** @test */
    public function a_thread_with_a_title_That_ends_in_a_number_should_generate_the_proper_slug()
    {
        $this->signIn();
        
        $thread = create('App\Thread', ['title' => 'Some Title 24']);
        
        $thread = $this->postJson(route('threads'), $thread->toArray() + ['g-recaptcha-response' => 'token'])->json();
    
        $this->assertEquals("some-title-24-{$thread['id']}", $thread['slug']);
        
    }

        /** @test */
    function unauthorized_users_may_not_delete_threads()
    {
        $thread  = create('App\Thread');
        $this->delete($thread->path())->assertRedirect('/login');
        
        $this->signIn();
        $this->delete($thread->path())->assertStatus(403);
        
    }


  

    /** @test */
    function authorized_users_can_delete_threads()
    {
        $this->signIn();

        $thread  = create('App\Thread', ['user_id' => auth()->id()]);
        $reply = create('App\Reply', ['thread_id' => $thread->id]);

        $response =  $this->json('DELETE', $thread->path());

        $response->assertStatus(204);
        
        $this->assertDatabaseMissing('threads', ['id' => $thread->id]);
        $this->assertDatabaseMissing('replies', ['id' => $reply->id]);
        $this->assertEquals(0, Activity::count());
    }

/** @test */
    function publishThread($overrides = [])
    {
        $this->signIn();

        $thread = make('App\Thread', $overrides);


        return $this->post(route('threads'), $thread->toArray());
    }
}
