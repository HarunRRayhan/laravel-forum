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
		$this->withExceptionHandling();

		$this->get('/threads/create')
		     ->assertRedirect('/login');

		$this->post('/threads')
		     ->assertRedirect('/login');

	}

    /** @test */
    function an_authenticated_user_can_create_new_forum_threads() {
		// Given we have a signedin user
	    $this->signIn();

	    // When we hit the endpoint to create new thread
		$thread = make('App\Thread');

		$response = $this->post('/threads', $thread->toArray());

	    // Then, we visit the thread page
	    $this->get($response->headers->get('Location'))
			->assertSee($thread->title)
			->assertSee($thread->body);

    }

    /** @test */
    function a_thread_requires_a_title() {

    	$this->publishThread(['title' => null])
		    ->assertSessionHasErrors('title');

    }

    /** @test */
    function a_thread_requires_a_body() {

    	$this->publishThread(['body' => null])
		    ->assertSessionHasErrors('body');

    }

    /** @test */
    function a_thread_requires_a_valid_channel() {

    	factory('App\Channel')->create();

    	$this->publishThread(['channel_id' => null])
		    ->assertSessionHasErrors('channel_id');

    	$this->publishThread(['channel_id' => 9999])
		    ->assertSessionHasErrors('channel_id');

    }

    /** @test **/
    public function guest_cannot_delete_threads() {
    	$this->withExceptionHandling();

	    $thread = create('App\Thread');
	    $response = $this->delete( $thread->path());

	    $response->assertRedirect('/login');
    }

    /** @test */
    function a_thread_can_be_deleted() {
    	$this->signIn();

    	$thread = create('App\Thread');
    	$reply = create('App\Reply', ['thread_id' => $thread->id]);

    	$response = $this->json('DELETE', $thread->path());

    	$response->assertStatus(204);

    	$this->assertDatabaseMissing('threads', ['id' => $thread->id]);
    	$this->assertDatabaseMissing('replies', ['id' => $reply->id]);
    }

    /** @test */
    function threads_may_only_be_deleted_by_those_who_have_permission() {
    	// TODO:
    }

    public function publishThread($overrides) {

        $this->withExceptionHandling()->signIn();

    	$thread = make('App\Thread', $overrides);

    	return $this->post('/threads', $thread->toArray());
    }

}
