<?php

namespace Appointer\AuthyApi\Exceptions;

use Throwable;

class InternalAuthyApiErrorException extends AuthyApiException
{
    public function __construct($message = "Internal authy api error occurred", $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

}
