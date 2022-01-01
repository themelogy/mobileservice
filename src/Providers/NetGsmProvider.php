<?php

namespace Themelogy\MobileService\Providers;

use Themelogy\MobileService\Entity\Account;
use Themelogy\MobileService\Entity\SMS;
use GuzzleHttp\Client;
use SimpleXMLElement;
use Themelogy\MobileService\Exceptions\SendMessageException;


class MobilDevProvider extends AbstractProvider
{
    private $apiURL = 'https://xmlapi.mobildev.com';

    private $errorCodes = [
        "01" => 'One or more parameters are missing or maybe mispelled (unknown resource or action)',
        "02" => "Not enough limit",
        "03" => "Not enough quota",
        "04" => "One or more parameters are missing or maybe mispelled (unknown resource or action)",
        "405" => "The method requested on the resource does not exist.",
        "429" => "Too Many Requests",
        "500" => "Ouch! Something went wrong on our side and we apologize! Please contact our support team who'll be able to help you on this",
        "503" => "The method requested on the resource does not exist.",
        "1001" => "Generic Error",
        "1002" => "Hatalı veya geçersiz AccountId veya Originator bilgisi",
        "1003" => "Domain alanı boş bırakılamaz",
        "1004" => "Hatalı veya geçersiz domain bilgisi, Lütfen domain listenizi kontrol edin.",
        "1005" => "SMS Listesi (items) eklenmemiş",
        "1006" => "SMS Listesinde kayıt bulunamadı",
        "1007" => "Gönderim için eklenilen expireAt tarihlerinden birisi hatalı",
        "1008" => "Hatalı veya geçersiz processId bilgisi",
        "1009" => "SMS Gönderimi için hesabınızda yeterli kredi bulunmamaktadır.",
        "9990" => "Authorization header missing",
        "9991" => "Authorization header must be base64",
        "9996" => "The resource with the specified ID you are trying to reach does not exist.",
        "9997" => "One or more parameters are missing or maybe mispelled (unknown resource or action)",
        "9998" => "You are not authorised to access this resource.",
        "9999" => "Hatalı Kullanıcı Adı / Parola"
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
        $element = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8" ?><MainmsgBody></MainmsgBody>');
        $element->addChild('UserName', $this->account->getUsername());
        $element->addChild('PassWord', $this->account->getPassword());
        $element->addChild('Action', 0);
        $element->addChild('Mesgbody', $sms->getMessage());
        $element->addChild('Numbers', $sms->getPhone());
        $element->addChild('AccountId');
        $element->addChild('Originator', $sms->getConfig('originator'));
        $element->addChild('Blacklist', $sms->getConfig('blacklist'));
        $element->addChild('SDate');
        $element->addChild('EDate');
        $element->addChild('Encoding', $sms->getConfig('encoding', 0));
        $element->addChild('MessageType', $sms->getConfig('messageType'));
        $element->addChild('RecipientType', $sms->getConfig('recipientType'));
        return $element->asXML();
    }

    protected function createAuthXML()
    {
        $element = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8" ?><MainReportRoot></MainReportRoot>');
        $element->addChild('UserName', $this->account->getUsername());
        $element->addChild('PassWord', $this->account->getPassword());
        $element->addChild('Action', 4);
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