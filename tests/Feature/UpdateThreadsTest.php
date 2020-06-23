<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UpdateThreadsTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_thread_requires_a_title_and_body_to_be_updated()
    {

        $this->signIn();

        $thread = create('App\Thread', ['user_id' => auth()->id()]);

        $this->patch($thread->path(), [
            'title' => 'changed',
        ])->assertSessionHasErrors('body');

        $this->patch($thread->path(), [
            'body' => 'changed body',
        ])->assertSessionHasErrors('title');
    }


    /** @test */
    public function unauthorized_users_may_not_update_threads()
    {
        $this->signIn();

        $thread = create('App\Thread', ['user_id' => create('App\User')->id]);

        $this->patchJson($thread->path(), [])->assertStatus(403);
    }


    /** @test */
    public function a_thread_can_be_updated_by_its_creator()
    {
        $this->signIn();

        $thread = create('App\Thread', ['user_id' => auth()->id()]);

        $this->patchJson($thread->path(), [
            'title' => 'changed',
            'body' => 'changed body'
        ]);

        $this->assertEquals('changed', $thread->fresh()->title);
        $this->assertEquals('changed body', $thread->fresh()->body);
    }
}
