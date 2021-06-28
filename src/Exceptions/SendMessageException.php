<?php

namespace Themelogy\MobileService\Exceptions;

use Exception;
use Throwable;

class SendMessageException extends Exception
{
    public function __construct($message = "Message has error when sending", $code = 335, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}