<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class SearchQueryTest extends TestCase
{
    use WithFaker;
    

    /**
     * Test the existancy of the GET /search endpoint
     */
    public function test_get_endpoint_search_returns_json() 
    {
        $response = $this->get('/search', ['query' => $this->faker->sentence]);

        $response->assertStatus(200)->assertJson([]);
    }
}
