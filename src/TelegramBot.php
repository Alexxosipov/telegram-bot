<?php

namespace Alexxosipov\Telegram;

use Alexxosipov\Telegram\Actions\ActionHandlerFactory;
use Alexxosipov\Telegram\Actions\Contracts\HasCallbackQuery;
use Alexxosipov\Telegram\Actions\Contracts\HasTextMessage;
use Alexxosipov\Telegram\Commands\CommandHandlerFactory;
use Alexxosipov\Telegram\Exceptions\TelegramBotException;
use Alexxosipov\Telegram\Models\TelegramUser;
use Alexxosipov\Telegram\Traits\DeletesMessage;
use Alexxosipov\Telegram\Traits\HandlesCommand;
use Alexxosipov\Telegram\Traits\RecognizesUser;
use Alexxosipov\Telegram\Traits\SendsResponse;
use BackedEnum;
use Telegram\Bot\Laravel\Facades\Telegram;
use Telegram\Bot\Objects\Update;

class TelegramBot
{
    use SendsResponse;
    use DeletesMessage;
    use RecognizesUser;
    use HandlesCommand;

    private TelegramUser $telegramUser;
    private ?BackedEnum $previousAction;

    public function __construct(
        public readonly ActionHandlerFactory   $actionHandlerFactory,
        private readonly CommandHandlerFactory $commandHandlerFactory,
    )
    {
    }

    public function handle(Update $update): void
    {
        $user = $this->getUserFromUpdate($update);

        if (!$user) {
            return;
        }

        $this->telegramUser = $user;
        $currentAction = $user->action;

        try {
            if ($update->message) {
                $this->handleMessage($update);

                return;
            }

            if ($update->callbackQuery) {
                $this->handleCallbackQuery($update);

                return;
            }
        } catch (\Throwable $e) {
            if ($this->telegramUser->action !== $currentAction) {
                // todo
            }

            report($e);
        }
    }

    public function handlePublicMessage(Update $update): void
    {

    }

    private function handleMessage(Update $update): void
    {
        if ($update->message->chat->type !== 'private') {
            $this->handlePublicMessage($update);

            return;
        }

        $this->deleteMessage($update->message->messageId);

        if ($update->message->hasCommand()) {
            $this->handleCommand($update);

            return;
        }

        $actionHandler = ActionHandlerFactory::make($this->telegramUser, $this);

        if (!$actionHandler instanceof HasTextMessage) {
            throw new TelegramBotException(
                sprintf('Class %d must implement HasCallbackQuery contract', get_class($actionHandler))
            );
        }

        $this->sendResponse($actionHandler->handleMessage($update->message->text));
    }

    /**
     * @throws \Throwable
     */
    private function handleCallbackQuery(Update $update): void
    {
        try {
            $callbackQuery = json_decode($update->callbackQuery->data, true);

            $action = config('telegram-bot.action-enum')::from((int)$callbackQuery['action']);

            $this->telegramUser->update([
                'action' => $action
            ]);

            $actionHandler = ActionHandlerFactory::make($this->telegramUser, $this);

            if (!$actionHandler instanceof HasCallbackQuery) {
                throw new TelegramBotException(
                    sprintf('Class %d must implement HasCallbackQuery contract', get_class($actionHandler))
                );
            }

            $this->sendResponse($actionHandler->handleCallbackQuery($callbackQuery ?? []));
        } catch (\Throwable $e) {
            \Telegram::answerCallbackQuery([
                'callback_query_id' => $update->callbackQuery->id,
                'show_alert' => true,
                'text' => 'Something went wrong'
            ]);

            if (!app()->isProduction()) {
                $exceptionMessage = sprintf(
                    implode("\n", [
                        '<b>File:</b> %d',
                        '<b>Line:</b> %d',
                        '<b>Message:</b> %d',
                        '<b>Code:</b> %d',
                        '<b>Trace:</b> %d',
                    ]),
                    $e->getFile(),
                    $e->getLine(),
                    $e->getMessage(),
                    $e->getCode(),
                    $e->getTraceAsString()
                );

                Telegram::sendMessage([
                    'chat_id' => $this->telegramUser->id,
                    'text' => $exceptionMessage,
                    'parse_mode' => 'HTML',
                    'disable_notification' => true,
                ]);
            }

            throw $e;
        }
    }
}