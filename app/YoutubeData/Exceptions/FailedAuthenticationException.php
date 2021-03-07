<?php

namespace App\YoutubeData\Exceptions;

class FailedAuthenticationException extends YoutubeDataException
{
    /**
     * @var string Default exception message used if the excpetion instance was created without a message
     */
    protected $defaultMessage = 'Failed to authenticate with google.';
}