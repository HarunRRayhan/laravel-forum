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
		$this->expectException(AuthenticationException::class);
		$thread = factory('App\Thread')->create();

		$reply = factory('App\Reply')->make();
		$this->post('/threads/' . $thread->id . '/replies', $reply->toArray());
	}

    /** @test */
    function an_authenticated_user_may_participate_in_forum_thread() {
		// Given we have an authenticated user
	    $this->be($user = factory('App\User')->create());

	    // And an existing thread
	    $thread = factory('App\Thread')->create();

	    // When the user adds a reply to the thread
	    $reply = factory('App\Reply')->make();
	    $this->post('/threads/' . $thread->id . '/replies', $reply->toArray());

	    // Then their reply should be included on the page
	    $this->get($thread->path())
		    ->assertSee($reply->body);

    }
}
