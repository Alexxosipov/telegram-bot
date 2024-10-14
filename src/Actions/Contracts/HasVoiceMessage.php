<?php

namespace Alexxosipov\TelegramBot\Actions\Contracts;

use Alexxosipov\TelegramBot\Response\Response;
use Telegram\Bot\Objects\Voice;

interface HasVoiceMessage
{
    public function handleVoiceMessage(Voice $voiceMessage): ?Response;
}