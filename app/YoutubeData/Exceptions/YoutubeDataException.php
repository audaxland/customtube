<?php

namespace App\YoutubeData\Exceptions;

class YoutubeDataException extends \Exception
{
    /**
     * @var string Default exception message used if the excpetion instance war created without a message
     */
    protected $defaultMessage = 'Failed to call Youtube Api';
    
    /**
     * Custom constructor to handle a default message to use if no message is provided
     * @param string $message
     * @param int $code
     * @param Throwable $previous
     */
    public function __construct(string $message = null , int $code = 0 , Throwable $previous = null) {
        parent::__construct($message ?? self::defaultMessage, $code, $previous);
    }
}