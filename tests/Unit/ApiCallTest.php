<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\YoutubeData\ApiCall;
use Illuminate\Foundation\Testing\WithFaker;

class ApiCallTest extends TestCase
{
    use WithFaker;

    public function test_apicall_search_restuns_json()
    {
        $apiCall = (new ApiCall())->fake();
        $searchResult = $apiCall->search($this->faker->sentence);
        $this->assertIsArray($searchResult);
        $this->assertEquals("youtube#searchListResponse", $searchResult['kind'] ?? null);
    }

}
