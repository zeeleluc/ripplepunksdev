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

        $payloadOptions = [
            'submit' => false,
            'return_url' => [
                'web' => config('services.xaman.webhook_url'),
                'app' => null,
            ],
        ];

        // If userToken is provided, add it to the payload for direct push
        if ($userToken) {
            $payloadOptions['user_token'] = $userToken;
        }

        $payload = new Payload(
            transactionBody: $transactionBody,
            options: new Options($payloadOptions)
        );

        return $this->sdk->createPayload($payload);
    }

    public function getPayload(string $uuid)
    {
        return $this->sdk->getPayload($uuid);
    }
}
