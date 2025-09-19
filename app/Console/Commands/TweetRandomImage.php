<?php

// app/Console/Commands/TweetRandomImage.php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\XPost;

class TweetRandomImage extends Command
{
    protected $signature = 'tweet:random-image';
    protected $description = 'Tweet a random image via XPost service';

    public function handle()
    {
        (new XPost())->tweetRandomImage();
        $this->info('Random image tweeted!');
    }
}
