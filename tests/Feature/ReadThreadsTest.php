<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class ReadThreadsTest extends TestCase
{
	use DatabaseMigrations;

	public function setUp() {
		parent::setUp();

		$this->thread = factory('App\Thread')->create();

	}

	/** @test */
    public function a_user_can_view_all_threads()
    {
        $response = $this->get('/threads');

        $response->assertSee($this->thread->title);
    }

    /** @test */
    function a_user_can_read_single_thread() {

	    $response = $this->get("/threads/{$this->thread->channel->slug}/{$this->thread->id}" );

	    $response->assertSee($this->thread->title);
    }

    /** @test */
    function a_user_can_read_replies_that_are_associated_with_a_thread() {
		$reply = factory('App\Reply')->create(['thread_id' => $this->thread->id ]);

	   $this->get("/threads/{$this->thread->channel->slug}/{$this->thread->id}" )
		   ->assertSee($reply->body);
    }

    /** @test */
    function a_user_can_filter_threads_according_to_a_channel() {
    	$channel = create('App\Channel');
    	$threadInChannel = create('App\Thread', ['channel_id' => $channel->id]);
    	$threadNotInChannel = create('App\Thread');

    	$this->get('/threads/' . $channel->slug)
		    ->assertSee($threadInChannel->title)
		    ->assertDontSee($threadNotInChannel->title);
    }
}
