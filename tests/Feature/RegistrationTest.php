<?php

namespace Tests\Feature;

use App\Mail\PleaseConfirmYourEmail;
use App\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
   use DatabaseMigrations;

   /** @test */
    public function a_confirmation_email_is_sent_upon_registration()
    {
        Mail::fake();
        
        $this->post(route('register'), [
            'name' => 'John',
            'email' => 'john@example.com',
            'password' => 'foobar',
            'password_confirmation' => 'foobar'
        ]);

        Mail::assertSent(PleaseConfirmYourEmail::class);

    }

    /** @test */
    public function users_can_fully_confirm_their_email_addresses()
    {
        Mail::fake();

        $this->post(route('register'), [
            'name' => 'John',
            'email' => 'john@example.com',
            'password' => 'foobar',
            'password_confirmation' => 'foobar'
        ]);

        $user = User::whereName('John')->first();

        $this->assertFalse($user->confirmed);
        $this->assertNotNull($user->confirmation_token);

        // Let the user confirm their account.  
        // route('foo', ['token'=>'thing'])  /foo?token=thing
        $response = $this->get(route('register.confirm',['token' => $user->confirmation_token]))
        ->assertRedirect(route('threads'));
        
        $this->assertTrue($user->fresh()->confirmed);

    }

    /** @test */
    public function confirming_an_invalid_token()
    {
        $this->get(route('register.confirm', ['token' => 'invalid']))
        ->assertRedirect(route('threads'))
        ->assertSessionHas('flash', 'Unknown token.');
    }
}
