<?php

namespace Themelogy\MobileService\Code;

use Themelogy\MobileService\Exceptions\ValidateCodeException;
use Themelogy\MobileService\Providers\AbstractProvider;

class CodeVerification
{
    /**
     * @var CodeProcessor
     */
    private $codeProcessor;

    /**
     * @var AbstractProvider
     */
    private $provider;

    private $instance;

    public function __construct(
        AbstractProvider $provider
    )
    {
        $this->codeProcessor = new CodeProcessor();
        $this->provider = $provider;
    }

    public function getVerificationCode($phone)
    {
        return $this->codeProcessor->getCode($phone);
    }

    public function sendVerificationCode($phone)
    {
        $code = $this->codeProcessor->generateCode($phone);
        $message = "Dogrulama kodunuz: $code";
        $date = \Carbon\Carbon::now()->format(config('mobile-service.date_format'));
        $response = $this->provider->sendSMS($phone, $message, $date);
        return $response;
    }

    public function validateVerificationCode($phone, $code)
    {
        if ($code = $this->codeProcessor->validateCode($phone, $code)) {
            return $code;
        }
        throw new ValidateCodeException('Doğrulama kodu hatalı!');
    }
}