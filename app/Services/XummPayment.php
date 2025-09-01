<?php

namespace App\Services;

use Xrpl\XummSdkPhp\XummSdk;
use Xrpl\XummSdkPhp\Payload\Payload;
use Xrpl\XummSdkPhp\Payload\Options;
use Xrpl\XummSdkPhp\Payload\ReturnUrl;

class XummPayment
{
    protected XummSdk $sdk;

    public function __construct()
    {
        $this->sdk = new XummSdk(
            config('services.xaman.api_key'),
            config('services.xaman.api_secret')
        );
    }

    public function createPaymentPayload(float $amount, string $destination, ?string $memo = null, ?string $userToken = null)
    {
        $transactionBody = [
            'TransactionType' => 'Payment',
            'Destination' => $destination,
            'Amount' => (string) ($amount * 1000000), // Convert XRP to drops
        ];

        if ($memo) {
            $transactionBody['Memos'] = [
                [
                    'Memo' => [
                        'MemoData' => bin2hex($memo),
                    ],
                ],
            ];
        }

        $customMeta = $userToken ? ['user_token' => $userToken] : [];

        $options = new Options(
            submit: false,
            returnUrl: new ReturnUrl(
                web: config('services.xaman.webhook_url'),
                app: null
            )
        );

        $payload = new Payload(
            transactionBody: $transactionBody,
            options: $options,
            customMeta: $customMeta // Pass user_token in custom_meta
        );

        return $this->sdk->createPayload($payload);
    }

    public function getPayload(string $uuid)
    {
        return $this->sdk->getPayload($uuid);
    }
}
