<?php

namespace Alexxosipov\TelegramBot\Artisan;

use Alexxosipov\TelegramBot\Response\Sender\ResponseSenderContract;
use Illuminate\Console\Command;

class SetWebhook extends Command
{
    protected $signature = 'telegram-bot:set-webhook';

    protected $description = 'Sets up telegram webhook';

    public function handle(ResponseSenderContract $senderContract): int
    {
        $senderContract->setWebhook([
            'url' => config('app.url') . config('telegram-bot.webhook-endpoint'),
            'secret_token' => config('telegram-bot.secret'),
            'drop_pending_updates' => true,
        ]);

        return 0;
    }
}