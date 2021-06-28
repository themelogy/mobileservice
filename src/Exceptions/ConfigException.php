<?php


namespace Themelogy\MobileService\Exceptions;

class ConfigException extends MobileServiceException
{
    public function getErrorCode(){
        return 200 + min($this->getCode(), 99);
    }
}