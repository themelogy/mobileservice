<?php

namespace Themelogy\MobileService\Providers;

use Themelogy\MobileService\Entity\Account;
use Themelogy\MobileService\Entity\SMS;
use GuzzleHttp\Client;
use Themelogy\MobileService\Extensions\SimpleXMLExtended;
use Themelogy\MobileService\Exceptions\SendMessageException;


class NetGsmProvider extends AbstractProvider
{
    private $apiURL = 'https://api.netgsm.com.tr/sms/send/xml';

    private $errorCodes = [
        "20" => "Mesaj metninde ki problemden dolayı gönderilemediğini veya standart maksimum mesaj karakter sayısını geçtiğini ifade eder. (Standart maksimum karakter sayısı 917 dir. Eğer mesajınız türkçe karakter içeriyorsa Türkçe Karakter Hesaplama menüsunden karakter sayılarının hesaplanış şeklini görebilirsiniz.)",
        "30" => "Geçersiz kullanıcı adı , şifre veya kullanıcınızın API erişim izninin olmadığını gösterir.Ayrıca eğer API erişiminizde IP sınırlaması yaptıysanız ve sınırladığınız ip dışında gönderim sağlıyorsanız 30 hata kodunu alırsınız. API erişim izninizi veya IP sınırlamanızı , web arayüzden; sağ üst köşede bulunan ayarlar> API işlemleri menüsunden kontrol edebilirsiniz.",
        "40" => "Mesaj başlığınızın (gönderici adınızın) sistemde tanımlı olmadığını ifade eder. Gönderici adlarınızı API ile sorgulayarak kontrol edebilirsiniz.",
        "50" => "Abone hesabınız ile İYS kontrollü gönderimler yapılamamaktadır.",
        "51" => "Aboneliğinize tanımlı İYS Marka bilgisi bulunamadığını ifade eder.",
        "70" => "Hatalı sorgulama. Gönderdiğiniz parametrelerden birisi hatalı veya zorunlu alanlardan birinin eksik olduğunu ifade eder.",
        "85" => "Mükerrer Gönderim sınır aşımı. Aynı numaraya 1 dakika içerisinde 20'den fazla görev oluşturulamaz."
    ];

    /**
     * @var Account
     */
    private $account;

    public function __construct(Account $account)
    {
        $this->account = $account;
    }

    public function sendSMS($phone, $message, $date)
    {
        try {
            $xml = $this->createSendXML(new SMS($phone, $message, $date, config('mobile-service')));
            $client = new Client();
            $promise = $client->request('POST', $this->apiURL, [
                'headers' => [
                    'Content-Type' => 'application/xml',
                ],
                'body' => $xml
            ]);
            return $promise->getBody()->getContents();
        } catch (\Exception $e) {
            throw new SendMessageException();
        }
    }

    public function checkAuth()
    {
        try {
            $xml = $this->createAuthXML();
            $client = new Client();
            $promise = $client->request('POST', $this->apiURL, [
                'headers' => [
                    'Content-Type' => 'application/xml',
                ],
                'body' => $xml
            ]);
            $contents = $promise->getBody()->getContents();
            $this->checkError($contents);
            return true;
        } catch (\Exception $e) {
            throw new \Exception("Doğrulama kodu servisi aktif değil. Lütfen bizimle iletişime geçiniz.");
        }
    }

    public function createSendXML(SMS $sms)
    {
        $element = new SimpleXMLExtended('<?xml version="1.0" encoding="UTF-8" ?><mainbody></mainbody>');

        $header = $element->addChild('header');
        $company = $header->addChild('company', 'Netgsm');
        $company->addAttribute("dil", "TR");
        $header->addChild('usercode', $this->account->getUsername());
        $header->addChild('password', $this->account->getPassword());
        $header->addChild('type', '1:n');
        $header->addChild('msgheader', 'ASLANLARPET');

        $body = $element->addChild('body');
        $body->addChildWithCData("msg", $sms->getMessage());
        $body->addChild('no', '0'.$sms->getPhone());

        return $element->asXML();
    }

    private function checkError($response)
    {
        $response = preg_split('#\r?\n#', $response, 0)[0];
        if(array_key_exists($response, $this->errorCodes)) {
            throw new \Exception("Doğrulama Hatası");
        }
    }
}