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

		$thread = make('App\Thread');

		$this->post('/threads', $thread->toArray());

	}

    /** @test */
    function an_authenticated_user_can_create_new_forum_threads() {
		// Given we have a signedin user
	    $this->signIn();

	    // When we hit the endpoint to create new thread
		$thread = make('App\Thread');

		$this->post('/threads', $thread->toArray());

	    // Then, we visit the thread page
	    $this->get($thread->path())

	    // We should see new thread
			->assertSee($thread->title)
			->assertSee($thread->body);

    }

}