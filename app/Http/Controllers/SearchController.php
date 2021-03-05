<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SearchController extends Controller
{
   
    /**
     * Handles a GET request for a search for youtube videos
     */
    public function searchQuery() {
        return ['resultCount' => 0, 'resultItems' => []];
    }
}
