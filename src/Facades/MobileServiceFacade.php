<?php

namespace Themelogy\MobileService\Facades;

use Illuminate\Support\Facades\Facade;
use Themelogy\MobileService\MobileService;

class MobileServiceFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return MobileService::class;
    }
}