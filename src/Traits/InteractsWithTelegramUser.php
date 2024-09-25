<?php

declare(strict_types=1);

namespace Alexxosipov\TelegramBot\Traits;

use Alexxosipov\TelegramBot\Response\Response;
use BackedEnum;

trait InteractsWithTelegramUser
{
    protected function updateUserAction(BackedEnum $action): void
    {
        $this->telegramUser->update([
            'action' => $action,
        ]);
    }

    protected function sendNextActionPreview(BackedEnum $action, ?array $data = []): Response
    {
        $this->updateUserAction($action);

        $actionHandler = $this->telegramBot->actionHandlerFactory->create(
            $this->telegramUser,
            $this->telegramBot
        );

        return $actionHandler->buildPreviewMessage($data);
    }
}
