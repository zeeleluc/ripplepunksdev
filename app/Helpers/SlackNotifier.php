<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Http;

class SlackNotifier
{
    public static function info(string $message)
    {
        self::send($message, ':information_source:');
    }

    public static function warning(string $message)
    {
        self::send($message, ':warning:');
    }

    public static function error(string $message)
    {
        self::send($message, ':x:');
    }

    protected static function send(string $message, string $icon)
    {
        $webhookUrl = config('services.slack.webhook_url');
        $channel = config('services.slack.channel', '#general');

        if (!$webhookUrl) {
            return;
        }

        Http::post($webhookUrl, [
            'channel' => $channel,
            'attachments' => [
                [
                    'fallback' => $message,
                    'color' => self::colorFromIcon($icon),
                    'text' => "{$icon} {$message}",
                ],
            ],
        ]);
    }

    protected static function colorFromIcon(string $icon): string
    {
        return match ($icon) {
            ':x:' => '#ff0000',              // red for error
            ':warning:' => '#ffae42',        // orange for warning
            ':information_source:' => '#36a64f', // green for info
            default => '#cccccc',
        };
    }
}
