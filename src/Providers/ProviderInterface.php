<?php

namespace Themelogy\MobileService\Providers;

interface ProviderInterface
{
    public function sendSMS($phone, $message, $date);
}