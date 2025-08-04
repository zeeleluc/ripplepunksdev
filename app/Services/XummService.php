<?php

namespace App\Services;

use Xrpl\XummSdkPhp\XummSdk;
use Xrpl\XummSdkPhp\Payload\Payload;
use Xrpl\XummSdkPhp\Payload\Options;
use Xrpl\XummSdkPhp\Payload\ReturnUrl;

class XummService
{
    protected XummSdk $sdk;

    public function __construct()
    {
        $this->sdk = new XummSdk(
            config('services.xaman.api_key'),
            config('services.xaman.api_secret')
        );
    }

    public function createLoginPayload()
    {
        $payload = new Payload(
            transactionBody: ['TransactionType' => 'SignIn'],
            options: new Options(
                submit: false,
                returnUrl: new ReturnUrl(
                    app: null,
                    web: 'https://jz5nodmthf.sharedwithexpose.com/xaman/webhook'
                )
            )
        );

        return $this->sdk->createPayload($payload);
    }

    public function getPayload(string $uuid)
    {
        return $this->sdk->getPayload($uuid);
    }
}
