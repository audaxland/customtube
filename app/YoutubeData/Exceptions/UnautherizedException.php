<?php

namespace App\YoutubeData\Exceptions;

class UnautherizedException extends YoutubeDataException
{
    /**
     * @var string Default exception message used if the excpetion instance was created without a message
     */
    protected $defaultMessage = 'You must be authenticated to access this ressource';
}