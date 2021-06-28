<?php

namespace Themelogy\MobileService;

use Themelogy\MobileService\Code\CodeVerification;
use Themelogy\MobileService\Entity\Account;
use Themelogy\MobileService\Exceptions\ValidateCodeException;

class MobileService
{
    private $provider;

    public function __construct()
    {
        $class = $this->getProviderClassName();
        $this->provider = new $class(new Account());
    }

    public function getProviderClassName()
    {
        $providerName = config('mobile-service.gateway');
        return "\\Themelogy\\MobileService\\Providers\\".ucfirst($providerName)."Provider";
    }

    public function checkAuth()
    {
        $this->provider->checkAuth();
    }

    public function getVerificationCode($phone)
    {
        return (new CodeVerification($this->provider))->getVerificationCode($phone);
    }

    public function sendVerificationCode($phone)
    {
        try {
            $promise = (new CodeVerification($this->provider))->sendVerificationCode($phone);
            return $promise;
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    public function validateVerificationCode($phone, $code)
    {
        return (new CodeVerification($this->provider))->validateVerificationCode($phone, $code);
    }
}