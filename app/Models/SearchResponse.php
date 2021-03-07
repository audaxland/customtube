<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SearchResponse extends Model
{
    use HasFactory;

    protected $guarded = [];

    /**
     * Eloquent belongs to relationshipt to the search_resquests table
     */
    public function searchRequest() {
        return $this->belongsTo(searchRequest::class);
    }

    /**
     * Eloquent many to many relationship to the youtube_videos table
     */
    public function youtubeVideo() {
        return $this->belongsToMany(YoutubeVideo::class);
    }
}
