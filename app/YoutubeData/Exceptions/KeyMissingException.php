<?php

namespace App\YoutubeData\Exceptions;

class KeyMissingException extends YoutubeDataException
{
    protected $defaultMessage = 'Api key missing, please configure the GOOGLE_API_KEY setting';
}