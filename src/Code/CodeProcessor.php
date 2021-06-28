<?php

namespace Themelogy\MobileService\Code;

use Themelogy\MobileService\Exceptions\ConfigException;
use Themelogy\MobileService\Exceptions\GenerateCodeException;
use Themelogy\MobileService\Exceptions\ValidateCodeException;
use Cache;

class CodeProcessor
{
    /**
     * Prefix for cache keys
     * @var string
     */
    private $cachePrefix = 'sms-code';

    /**
     * Code length
     * @var int
     */
    private $codeLength = 6;

    /**
     * Lifetime of codes in minutes
     * @var int
     */
    private $minutesLifetime = 3;

    public function __construct()
    {
        $this->cachePrefix = (string) config('mobile-service.cache-prefix', $this->cachePrefix);
        $this->codeLength = config('mobileservice.code-length', $this->codeLength);

        if (empty($this->codeLength) || !is_int($this->codeLength))
        {
            throw new ConfigException("config/mobile-service.php belirlediğiniz kod uzunluğu sağlanamadı.");
        }

        $this->minutesLifetime = config('mobile-service.code-lifetime', $this->minutesLifetime);
        if (empty($this->minutesLifetime) || !is_int($this->minutesLifetime)) {
            throw new ConfigException('config/sms-verification.php belirlediğiniz kod süresi sağlanamadı.');
        }
    }

    public function generateCode($phoneNumber)
    {
        try {
            $randomFunction = 'random_int';
            if (!function_exists($randomFunction))
            {
                $randomFunction = 'mt_rand';
            }
            $code = $randomFunction(pow(10, $this->codeLength - 1), pow(10, $this->codeLength) - 1);
            Cache::put($this->cachePrefix.$this->trimPhoneNumber($phoneNumber), $code, $this->minutesLifetime);
            return $code;
        } catch (\Exception $e)
        {
            throw new GenerateCodeException('Kod oluşturma hatası', 0, $e);
        }
    }

    public function getCode($phoneNumber)
    {
        if ($codeValue = Cache::get($this->cachePrefix.$this->trimPhoneNumber($phoneNumber))) {
            return $codeValue;
        }
        return false;
    }

    public function validateCode($phoneNumber, $code)
    {
        $codeValue = Cache::get($this->cachePrefix.$this->trimPhoneNumber($phoneNumber));
        if ($codeValue == $code) {
            Cache::forget($this->cachePrefix.$this->trimPhoneNumber($phoneNumber));
            return $codeValue;
        }
        return false;
    }

    /**
     * @param $phoneNumber
     * @return string
     */
    private function trimPhoneNumber($phoneNumber){
        return trim(ltrim($phoneNumber, '+'));
    }

    /**
     * @return int Seconds
     */
    public function getLifetime(){
        return $this->minutesLifetime * 60;
    }
}