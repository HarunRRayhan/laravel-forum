<?php

namespace Tests\Feature;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class CreateThreadsTest extends TestCase
{
	use DatabaseMigrations;

	/** @test */
	function guest_may_not_create_thread() {
		$this->expectException(AuthenticationException::class);

		$thread = factory('App\Thread')->make();

		$this->post('/threads', $thread->toArray());

	}

    /** @test */
    function an_authenticated_user_can_create_new_forum_threads() {
		// Given we have a signedin user
		$this->actingAs(factory('App\User')->create());

	    // When we hit the endpoint to create new thread
		$thread = factory('App\Thread')->make();

		$this->post('/threads', $thread->toArray());

	    // Then, we visit the thread page
	    $this->get($thread->path())

	    // We should see new thread
			->assertSee($thread->title)
			->assertSee($thread->body);

    }

}
