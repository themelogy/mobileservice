<?php


namespace Themelogy\MobileService\Exceptions;

class GenerateCodeException extends MobileServiceException
{
    public function getErrorCode()
    {
        return 500 + min($this->getCode(), 99);
    }
}