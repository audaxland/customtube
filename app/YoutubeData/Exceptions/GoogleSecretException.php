<?php

namespace App\YoutubeData\Exceptions;

class GoogleSecretException extends YoutubeDataException
{
    /**
     * @var string Default exception message used if the excpetion instance was created without a message
     */
    protected $defaultMessage = 'Google Api OAuth settings are not configure on this server, please set up the client_secret.json file.';
}