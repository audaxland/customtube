<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SearchRequest extends Model
{
    use HasFactory;

    protected $guarded = [];

    /**
     * Eloquent has many relationship to the search_responses table
     */
    public function searchResponse() {
        return $this->hasMany(SearchResponse::class);
    }

    /**
     * Eloquent has one relationship to the last search_responses table record for this request
     */
    public function latestSearchResponse() {
        return $this->hasOne(SearchResponse::class)->latest();
    }
}
