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
        $url = config('app.url') . config('telegram-bot.webhook-endpoint');

        $senderContract->setWebhook([
            'url' => config('app.url') . config('telegram-bot.webhook-endpoint'),
            'secret_token' => config('telegram-bot.secret'),
            'drop_pending_updates' => true,
        ]);

        $this->info("Webhook set to $url");

        return 0;
    }
}