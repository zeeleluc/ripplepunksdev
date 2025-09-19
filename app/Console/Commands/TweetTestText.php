<?php

// app/Console/Commands/TweetRandomImage.php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\XPost;

class TweetTestText extends Command
{
    protected $signature = 'tweet:test-text';
    protected $description = 'Tweet a test text via XPost service';

    public function handle()
    {
        $xPost = new XPost();
        $xPost->setText('Test');
        $xPost->post();
    }
}
