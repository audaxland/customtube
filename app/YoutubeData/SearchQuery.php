<?php

namespace App\YoutubeData;

use App\Models\SearchRequest;
use App\Models\SearchResponse;
use App\Models\YoutubeVideo;
use App\YoutubeData\ApiCall;
use Illuminate\Support\Carbon;
use App\YoutubeData\Exceptions\YoutubeDataException;

class SearchQuery
{
    /**
     * @var integer $cacheLifeTime number of seconds, expire time of the cached results in the database
     */
    protected $cacheLifeTime = 3600*24;
    
    /**
     * Handles a search query either form the cache, if available or from the youtube api
     * and returns the results in an array
     * @param string $queryString : text to search for
     * @param integer $page : pagination page of the result (this is independant of the actuall call to the api)
     * @param integer $itemsPerPage : pagination items per page of the result, (this is independant of the actuall call to the api)
     * @return array  
     *          - integer totalResults : number of results for the query that exists on youtube
     *          - integer maxQuerable : number of results already in cache + 50 (because we can only fetch 50 resutls at once, and searches are expensive, so we don't want to allow the user to skip pages)
     *          - integer page : the results page number
     *          - integer itemsPerPage : the results pagination items per page  
     *          - array videos : the list of videos of the response
     * @throws YoutubeDataException if error when calling the api
     */
    public function searchFor($queryString, $page = 1, $itemsPerPage = 10)
    {
        $searchRequest = $this->getSearchRequest($queryString);
        return [
            'searchQuery'   => $queryString,
            'totalResults'  => $searchRequest->total_results,
            'maxQuerable'   => min( $searchRequest->fetched_results + 50, $searchRequest->total_results),
            'page'          => $page,
            'itemsPerPage'  => $itemsPerPage,
            'videos'        => $this->getItemsPage($searchRequest, $page, $itemsPerPage)
        ];
    }

    /**
     * Gets an instance of the eloquent model of the search_requests table for a query
     * this will read an existing record if available,
     * otherwhise  will create a new record and related tables records after calling the youtube api
     * @param string $queryString : text to query
     * @return SearchRequest
     * @throws YoutubeDataException if error when calling the api
     */
    protected function getSearchRequest($queryString) {
        
        $existingRequest = SearchRequest::where('query', $queryString)
                                        ->whereDate('created_at', '>=', Carbon::now()->subSecond($this->cacheLifeTime))
                                        ->latest()
                                        ->first();
                                        
        if ($existingRequest) return $existingRequest;
        return $this->fetchNextResponse(SearchRequest::make([
            'query' => $queryString,
        ]));

    }

    /**
     * Gets a new response for a given request, 
     * This will call the youtube api and fetch 50 more results for a given query
     * The results are then stored in the database
     * @param SearchRequest : instance of the eloquent model for the request
     * @return SearchRequest : same instance as the parameter, allows fluent call
     * @throws YoutubeDataException
     */
    protected function fetchNextResponse(SearchRequest $searchRequest) 
    {
        $ApiCall = new ApiCall();
        $options = [];
        if ($searchRequest->last_page_token) {
            $options['pageToken'] = $searchRequest->last_page_token;
        }
        $searchResult = $ApiCall->search($searchRequest->query, $options);
        
        if (empty($searchResult['kind']) || ($searchResult['kind'] != 'youtube#searchListResponse')) {
            throw new YoutubeDataException('Unkown result kind: ' . ($searchResult['kind'] ?? 'not set'));
        }

        $previouslyFetchedResults = ($searchRequest->fetched_results ?? 0);

        $searchRequest->total_results = $searchRequest->total_results ?? $searchResult['pageInfo']['totalResults'] ?? 0;
        $searchRequest->fetched_results =  $previouslyFetchedResults + count($searchResult['items'] ?? []);
        $searchRequest->last_page_token = $searchResult['nextPageToken'] ?? null;
        $searchRequest->save();

        
        $current_list_count = count($searchResult['items'] ?? []);
        $searchResponse = SearchResponse::create([
            'search_request_id'     => $searchRequest->id,
            'etag'                  => $searchResult['etag'] ?? '',
            'next_page_token'       => $searchResult['nextPageToken'] ?? null,
            'region_code'           => $searchResult['regionCode'] ?? '',
            'total_results'         => $searchResult['pageInfo']['totalResults'] ?? 0,
            'results_per_page'      => $searchResult['pageInfo']['resultsPerPage'] ?? 0,
            'current_list_count'    => count($searchResult['items'] ?? []),
            'result_order_first'    => $previouslyFetchedResults + ($current_list_count ? 1 : 0),
            'result_order_last'     => $previouslyFetchedResults + $current_list_count,
        ]);

        foreach($searchResult['items'] ?? [] as $index => $item) {
            $youtubeVideo = $this->updateOrNewYoutubeVideo($item);
            $searchResponse->youtubeVideo()->attach($youtubeVideo, [
                'response_order'    => $index + $searchResponse->result_order_first,
                'created_at'        => now(),
                'updated_at'        => now()
            ]);
        };
        $searchResponse->save();
        return $searchRequest;

    }

    /**
     * Finds the eloquent model of the youtube_searches table for a given search result video
     * or creates a new record if not already in the database
     * @param array $searchResult : video item from the response of a search on the youtube api
     * @return YoutubeVideo Eloquent model of the video record in the database
     * @throws YoutubeDataException if $searchResult is not a valid video response
     */
    protected function updateOrNewYoutubeVideo($searchResult) {
        if (empty($searchResult['id']['videoId'])) {
            throw new YoutubeDataException('Corrupted youtue video data');
        }
        $youtubeVideo = YoutubeVideo::where('video_id', $searchResult['id']['videoId'])->first();
        if (empty($youtubeVideo)) {
            $youtubeVideo = YoutubeVideo::make(['video_id' => $searchResult['id']['videoId']]);
        } 
        $youtubeVideo->etag = $searchResult['etag'] ?? '';
        $youtubeVideo->raw = json_encode($searchResult ?? '{}');
        $youtubeVideo->updated_at = now();
        $youtubeVideo->save();
        return $youtubeVideo;
    }

    /**
     * Gets a list of videos for the request
     * @param SearchRequest $searchRequest : Eloquent model instance of the request
     * @param int $page : request pagination page
     * @param int $itemsPerPage : request pagination items per page
     * @return array List of videos requested
     */
    protected function getItemsPage(SearchRequest $searchRequest, $page = 1, $itemsPerPage = 10) {
        // If page requested is greater than the last possible page, there is no item to return
        if ( (($page-1)*$itemsPerPage) > $searchRequest->total_results) return [];

        // Searches are expensive, so we want to prevent searching far ahead
        if ( (($page-1)*$itemsPerPage) > ($searchRequest->fetched_results + 50)) return [];

        // If we need to fetch the next 50 results before responding, to the resquest, we do so now
        if ( (($page*$itemsPerPage) > $searchRequest->fetched_results)
                && ($searchRequest->fetched_results < $searchRequest->total_results)
        ) {
            $this->fetchNextResponse($searchRequest)->refresh();
        }

        $responses = $searchRequest->SearchResponse()
                        ->where('result_order_first', '<=', $page*$itemsPerPage)
                        ->where('result_order_last', '>=', (($page-1)*$itemsPerPage) + 1)
                        ->orderBy('result_order_first')
                        ->get();
        $items = [];

        foreach($responses as $response ) {
            $items = array_merge(
                $items, 
                $response->youtubeVideo()
                    ->wherePivot('response_order', '>', (($page-1)*$itemsPerPage))
                    ->wherePivot('response_order', '<=', ($page*$itemsPerPage))
                    ->withPivot('response_order')
                    ->get()
                    ->transform(function ($item, $key) {
                        $details = json_decode($item['raw'], true);
                        return [
                            'search_id' => $item['pivot']['response_order'],
                            'video_id'  => $item['video_id'],
                            'etag'      => $item['etag'],
                        ] + $details['snippet'] ;
                    })
                    ->toArray()
            );
        }

        return $items;

    }
}