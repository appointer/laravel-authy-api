<?php

namespace Appointer\AuthyApi\Exceptions;

use Throwable;

class BadRequestException extends AuthyApiException
{
    public function __construct($message = "Bad request", $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

}
