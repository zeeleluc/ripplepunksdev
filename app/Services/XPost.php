<?php

namespace App\Services;

use Abraham\TwitterOAuth\TwitterOAuth;
use Abraham\TwitterOAuth\TwitterOAuthException;
use App\Services\Image\XrpTrendImage;
use Illuminate\Support\Facades\Storage;

class XPost
{
    private string $oauthId = '';
    private string $text = '';
    private array $images = []; // supports multiple images

    private string $consumerKey;
    private string $consumerSecret;
    private string $tokenIdentifier;
    private string $tokenSecret;

    public function __construct()
    {
        $this->loadCredentialsFromConfig();
    }

    private function loadCredentialsFromConfig(): void
    {
        $config = config('services.twitter');

        $this->consumerKey     = $config['consumer_key'];
        $this->consumerSecret  = $config['consumer_secret'];
        $this->tokenIdentifier = $config['access_token'];
        $this->tokenSecret     = $config['access_token_secret'];

        $this->oauthId = env('TWITTER_USER_ID', '');
    }

    /** ---------------------
     * Set content & images
     * --------------------- */
    public function setText(string $text): self
    {
        $this->text = $text;
        return $this;
    }

    public function addImage(string $path): self
    {
        $this->images[] = $path;
        return $this;
    }

    public function setImages(array $paths): self
    {
        $this->images = $paths;
        return $this;
    }

    public function getImages(): array
    {
        return $this->images;
    }

    /** ---------------------
     * Clear temp images
     * --------------------- */
    public function clear(): self
    {
        foreach ($this->images as $image) {
            if ($image && file_exists($image)) {
                @unlink($image);
            }
        }
        $this->images = [];
        return $this;
    }

    /** ---------------------
     * Post tweet
     * --------------------- */
    public function post(): array
    {
        try {
            $params = ['text' => html_entity_decode($this->text)];
            $this->attachMedia($params);

            return $this->send('tweets', $params, '2');
        } finally {
            $this->clear();
        }
    }

    public function reply(string $tweetId): array
    {
        $params = [
            'text' => html_entity_decode($this->text),
            'reply' => ['in_reply_to_tweet_id' => $tweetId],
        ];

        $this->attachMedia($params);

        return $this->send('tweets', $params, '2');
    }

    /** ---------------------
     * Attach media to tweet
     * --------------------- */
    private function attachMedia(array &$params): void
    {
        if (empty($this->images)) return;

        $mediaIds = [];
        foreach ($this->images as $image) {
            $upload = $this->connection('1.1')->upload('media/upload', [
                'media' => $image,
            ], ['chunkedUpload' => false]);

            if (!empty($upload->media_id_string)) {
                $mediaIds[] = $upload->media_id_string;
            }
        }

        if ($mediaIds) {
            $params['media'] = [
                'tagged_user_ids' => $this->oauthId ? [$this->oauthId] : [],
                'media_ids' => $mediaIds,
            ];
        }
    }

    /** ---------------------
     * Send request
     * --------------------- */
    private function send(string $endpoint, array $params, string $version): array
    {
        $connection = $this->connection($version);
        $response = $connection->post($endpoint, $params, ['jsonPayload' => true]);
        return json_decode(json_encode($response), true);
    }

    /** ---------------------
     * Twitter connection
     * --------------------- */
    private function connection(string $version): TwitterOAuth
    {
        $twitter = new TwitterOAuth(
            $this->consumerKey,
            $this->consumerSecret,
            $this->tokenIdentifier,
            $this->tokenSecret
        );

        $twitter->setApiVersion($version);
        $twitter->setTimeouts(30, 60);

        return $twitter;
    }

    /** ---------------------
     * Download random OG RipplePunk
     * --------------------- */
    private function getRandomOGRipplePunkImage(): string
    {
        $id = rand(0, 10999);
        $remotePath = "ogs/{$id}.png";
        $tempPath = storage_path("app/tmp/twitter_post_{$id}.png");

        if (!file_exists(dirname($tempPath))) {
            mkdir(dirname($tempPath), 0755, true);
        }

        $contents = Storage::disk('spaces')->get($remotePath);
        file_put_contents($tempPath, $contents);

        return $tempPath;
    }

    /** ---------------------
     * Tweet a GM post
     * --------------------- */
    public function tweetGm(): void
    {
        $this->setText('GM XRPL!')
            ->addImage($this->getRandomOGRipplePunkImage())
            ->post();
    }

    /** ---------------------
     * Tweet left/right post
     * --------------------- */
    public function tweetLeftRight(): void
    {
        $this->setText('Left or Right?')
            ->addImage($this->getRandomOGRipplePunkImage())
            ->addImage($this->getRandomOGRipplePunkImage())
            ->post();
    }

    /** ---------------------
     * Tweet XRP Trend chart
     * --------------------- */
    public function tweetXRPTrendImage(): void
    {
        $trendImage = new XrpTrendImage();
        $trendImage->process();

        // Save chart to Spaces using the class' date-based naming
        $spacesPath = $trendImage->saveToSpaces();

        // Download it locally for Twitter API
        $localPath = storage_path('app/tmp/' . basename($spacesPath));
        if (!file_exists($localPath)) {
            $contents = Storage::disk('spaces')->get($spacesPath);
            file_put_contents($localPath, $contents);
        }

        $this->setText("XRP 24h Trend - NY Timezone")
            ->addImage($localPath)
            ->post();
    }
}
