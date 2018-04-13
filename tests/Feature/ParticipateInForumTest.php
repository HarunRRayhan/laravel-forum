<?php

namespace Tests\Feature;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class ParticipateInForumTest extends TestCase
{
	use DatabaseMigrations;

	/** @test */
	function unauthenticated_user_may_not_add_replies() {
		$this->withExceptionHandling()
			->post('/threads/some-channel/1/replies', [])
			->assertRedirect('/login');
	}

    /** @test */
    function an_authenticated_user_may_participate_in_forum_thread() {
		// Given we have an authenticated user
	    $this->signIn();

	    // And an existing thread
	    $thread = create('App\Thread');

	    // When the user adds a reply to the thread
	    $reply = make('App\Reply');
	    $this->post($thread->path(). '/replies', $reply->toArray());

	    // Then their reply should be included on the page
	    $this->get($thread->path())
		    ->assertSee($reply->body);

    }
}
