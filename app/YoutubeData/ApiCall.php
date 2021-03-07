<?php

namespace App\YoutubeData;

use Illuminate\Support\Facades\Http;
use App\YoutubeData\Exceptions\KeyMissingException;
use App\YoutubeData\Exceptions\ServiceException;

class ApiCall 
{

    /**
     * @var array $fakeUrls : list of urls that are set with the faker
     */
    static protected $fakeUrls = null;

    /**
     * The Youtue API only allows up to 100 searches per day, 
     * so we can't afford to use the actual endpoint for dev and testing
     * setupFaker() sets the Http client to use a fake api for searches
     */
    static public function setupFaker()
    {
        if (self::$fakeUrls) return;
        $searchResponseBody = file_get_contents(__DIR__.'/dummy/searchListResponse.json');
        self::$fakeUrls = [
            'youtube.googleapis.com/youtube/v3/search*' => Http::response($searchResponseBody, 200),
        ];
        Http::fake(self::$fakeUrls);
    }

    /**
     * Fluent function to set up the faker for youtube api searches
     */
    public function fake() 
    {
        self::setupFaker();
        return $this;
    }

    /**
     * The api key that we read dynamicaly from the config file
     * @var string|null $apiKey
     */
    protected $apiKey = null;

    /**
     * Read the Api key from the configuration file
     */
    protected function getApiKey() 
    {
        if (empty($this->apiKey)) {
            $this->apiKey = config('googleapi.apikey');
            if (empty($this->apiKey)) throw new KeyMissingException();
        }
        return $this->apiKey;
    }

    /**
     * @param string $queryString : The text to search for
     * @param array $options : optional parameters to send to the api
     */
    public function search($queryString, $options = []) 
    {
        // Uncomment the following line during dev/testing
        // $this->fake();
        
        $url = 'https://youtube.googleapis.com/youtube/v3/search';
        $options = array_merge([
            'q' => urlencode($queryString),
            'part'          => 'snippet',
            'maxResults'    => 50,
            'type'          => 'video',
            'key'       => self::getApiKey(),
        ], $options);

        $apiCall = Http::get($url, $options);
        if ($apiCall->failed()) throw new ServiceException();
        return $apiCall->json();
    }
}
