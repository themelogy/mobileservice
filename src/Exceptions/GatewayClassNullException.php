<?php

namespace Themelogy\MobileService\Exceptions;

use Exception;
use Throwable;

class GatewayClassNullException extends Exception
{
    public function __construct($message = "Class must be specified!", $code = 331, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}