<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\YoutubeData\ApiCall;
use App\Models\SearchRequest;

class SearchQueryTest extends TestCase
{
    use WithFaker, RefreshDatabase;
    

    /**
     * Test the existancy of the GET /search endpoint
     */
    public function test_get_endpoint_search_returns_json() 
    {
        ApiCall::setupFaker();
        $response = $this->get('/search?query=' . urlencode($this->faker->sentence));

        $response->assertStatus(200)->assertJson([]);
    }

    /**
     * Test that requests are stored in the search_requests table
     */
    public function test_search_queries_are_stored_in_the_database()
    {
        ApiCall::setupFaker();
        $sentence = trim($this->faker->sentence, '.');
        $response = $this->get('/search?query=' . urlencode($sentence));

        $this->assertDatabaseHas('search_requests', ['query' => $sentence]);

    }

    /**
     * Test that a new response is stored in the search_responses table
     */
    public function test_search_responses_are_stored_in_database()
    {
        ApiCall::setupFaker();
        $sentence = trim($this->faker->sentence, '.');
        $response = $this->get('/search?query=' . urlencode($sentence));

        $lastRequest = SearchRequest::latest()->first();

        $this->assertDatabaseHas('search_responses', ['search_request_id' => $lastRequest->id]);
    }
}
