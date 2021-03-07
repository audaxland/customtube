<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\YoutubeData\SearchQuery;

class SearchController extends Controller
{
   
    /**
     * Handles a GET request for a search for youtube videos
     */
    public function searchYoutubeApi() {
        request()->validate([
            'query'         => 'required',
            'page'          => 'integer',
            'itemsPerPage'  => 'integer',
        ]);
        $searchQuery = new SearchQuery();
        return $searchQuery->searchFor(strip_tags(request('query')), request('page', 1), request('itemsPerPage', 10));
    }

}

