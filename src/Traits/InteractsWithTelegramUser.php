<?php

declare(strict_types=1);

namespace Alexxosipov\Telegram\Traits;

use Alexxosipov\Telegram\Response\Response;
use BackedEnum;

trait InteractsWithTelegramUser
{
    protected function updateUserAction(BackedEnum $action): void
    {
        $this->telegramUser->update([
            'action' => $action,
        ]);
    }

    protected function sendNextActionPreview(BackedEnum $action, ?array $data = null): Response
    {
        $this->updateUserAction($action);

        $actionHandler = $this->telegramBot->actionHandlerFactory->create(
            $this->telegramUser,
            $this->telegramBot
        );

        return $actionHandler->buildPreviewMessage($data);
    }
}
