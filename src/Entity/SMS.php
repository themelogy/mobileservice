<?php

namespace Themelogy\MobileService\Entity;

use Themelogy\MobileService\Entity\Enums\MessageType;
use Themelogy\MobileService\Entity\Enums\RecipientType;

class SMS
{
    protected $message;

    protected $phone;

    protected $date;

    protected $encoding = 0;

    protected $config;

    public function __construct($phone, $message, $date, $config = [])
    {
        $this->message = $message;
        $this->phone = $phone;
        $this->date = $date;

        $this->config = $config;
        $this->config['messageType'] = MessageType::Information;
        $this->config['recipientType'] = '';
    }

    public function getConfig($name, $default = '')
    {
        if(in_array($name, $this->config) && empty($default)) {
            return $this->config[$name];
        }
        return !empty($default) ? $default : '';
    }

    public function setConfig($name, $value)
    {
        return $this->config[$name] = $value;
    }

    public function getMessage()
    {
        return $this->message;
    }

    public function getPhone()
    {
        return $this->phone;
    }

    public function getDate()
    {
        return $this->date;
    }
}