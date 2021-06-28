<?php


namespace Themelogy\MobileService\Providers;


use Themelogy\MobileService\Entity\SMS;

abstract class AbstractProvider implements ProviderInterface
{
    protected function createSendXML(SMS $sms)
    {

    }

    protected function createAuthXML()
    {

    }
}