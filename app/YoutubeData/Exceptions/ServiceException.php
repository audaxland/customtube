<?php

namespace App\YoutubeData\Exceptions;

class ServiceException extends YoutubeDataException
{
    /**
     * @var string Default exception message used if the excpetion instance war created without a message
     */
    protected $defaultMessage = 'The youtube api service is currently unavailable';
}