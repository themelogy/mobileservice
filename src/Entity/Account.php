<?php

namespace Themelogy\MobileService\Entity;

class Account
{
    protected $username;
    protected $password;
    /**
     * @var string
     */
    private $vendorCode;

    public function __construct($username = '', $password = '', $vendorCode = '')
    {
        $this->username = config('mobile-service.username', $username);
        $this->password = config('mobile-service.password', $password);
        $this->vendorCode = config('mobile-service.vendor_code', $vendorCode);
    }

    public function getUsername()
    {
        return $this->username;
    }

    public function getPassword()
    {
        return $this->password;
    }

    public function getVendorCode()
    {

    }
}