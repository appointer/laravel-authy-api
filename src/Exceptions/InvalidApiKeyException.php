<?php

namespace Appointer\AuthyApi\Exceptions;

use Throwable;

class InvalidApiKeyException extends AuthyApiException
{
    public function __construct($message = "Invalid authy API key provided", $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
