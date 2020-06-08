<?php

namespace Tests\Unit;

use App\Reply;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

//use PHPUnit\Framework\TestCase;

class ReplyTest extends TestCase
{
    use DatabaseMigrations;
    /** @test */
    function it_has_an_owner()
    {
        $reply = create('App\Reply');

        $this->assertInstanceOf('App\User', $reply->owner);
        
    }

    /** @test */
    function it_knows_if_it_was_just_published()
    {
        $reply = create('App\Reply');

        $this->assertTrue($reply->wasJustPublished());
        
        $reply->created_at = Carbon::now()->subMonth();
        
        $this->assertFalse($reply->wasJustPublished());


    }


    /** @test */
    function it_wraps_mentionned_usernames_in_the_body_within_anchor_tags()
    {
        $reply  = new Reply([
            'body' => 'Hello @JaneDoe'
        ]);
            
        $this->assertEquals(
            'Hello <a href="/profiles/JaneDoe">@JaneDoe</a>', $reply->body
        );
    }

}

