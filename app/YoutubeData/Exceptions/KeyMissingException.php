<?php

namespace App\YoutubeData\Exceptions;

class KeyMissingException extends YoutubeDataException
{
    /**
     * @var string Default exception message used if the excpetion instance was created without a message
     */
    protected $defaultMessage = 'Api key missing, please configure the GOOGLE_API_KEY setting';
}