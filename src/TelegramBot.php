<?php

namespace Alexxosipov\TelegramBot;

use Alexxosipov\TelegramBot\Actions\ActionHandlerFactory;
use Alexxosipov\TelegramBot\Actions\Contracts\HasCallbackQuery;
use Alexxosipov\TelegramBot\Actions\Contracts\HasTextMessage;
use Alexxosipov\TelegramBot\Actions\Contracts\HasVoiceMessage;
use Alexxosipov\TelegramBot\Actions\Contracts\KeepsPreviousMessage;
use Alexxosipov\TelegramBot\Commands\CommandHandlerFactory;
use Alexxosipov\TelegramBot\Exceptions\TelegramBotException;
use Alexxosipov\TelegramBot\Models\TelegramUser;
use Alexxosipov\TelegramBot\Response\Response;
use Alexxosipov\TelegramBot\Response\Sender\ResponseSenderContract;
use Alexxosipov\TelegramBot\Traits\HandlesCommand;
use Alexxosipov\TelegramBot\Traits\RecognizesUser;
use BackedEnum;
use Illuminate\Http\Request;
use Symfony\Bridge\PsrHttpMessage\Factory\PsrHttpFactory;
use Telegram\Bot\Laravel\Facades\Telegram;
use Telegram\Bot\Objects\Update;

class TelegramBot
{
    use RecognizesUser;
    use HandlesCommand;

    private TelegramUser $telegramUser;
    private ?BackedEnum $previousAction;

    public function __construct(
        public readonly ActionHandlerFactory   $actionHandlerFactory,
        private readonly CommandHandlerFactory $commandHandlerFactory,
        public readonly ResponseSenderContract $responseSender
    )
    {
    }

    public function handleFromRequest(Request $request): void
    {
        /** @var TelegramBot $bot */
        $bot = app(static::class);
        $bot->handle(
            Telegram::getWebhookUpdate(request: (new PsrHttpFactory)->createRequest($request))
        );
    }

    public function handle(Update $update): ?Response
    {
        $user = $this->getUserFromUpdate($update);

        if (!$user) {
            return null;
        }

        $this->telegramUser = $user;

        try {
            $response = null;

            if ($update->message) {
                $response = $this->handleMessage($update);
            }

            if ($update->callbackQuery) {
                $response = $this->handleCallbackQuery($update);
            }

            $this->responseSender->send($this->telegramUser, $response);

            return $response;
        } catch (\Throwable $e) {
            report($e);

            return null;
        }
    }

    public function handlePublicMessage(Update $update): void
    {
    }

    private function handleMessage(Update $update): ?Response
    {
        if ($update->message->chat->type !== 'private') {
            $this->handlePublicMessage($update);

            return null;
        }

        if ($update->message->hasCommand()) {
            $this->responseSender->deleteMessage($this->telegramUser, $update->message->messageId);

            return $this->handleCommand($update);
        }

        $actionHandler = $this->actionHandlerFactory->create($this->telegramUser, $this);

        if (!$actionHandler instanceof HasTextMessage) {
            throw new TelegramBotException(
                sprintf('Class %d must implement HasTextMessage contract', get_class($actionHandler))
            );
        }

        if (!$actionHandler instanceof KeepsPreviousMessage) {
            $this->responseSender->deleteMessage($this->telegramUser, $update->message->messageId);
        }

        if ($update->message->voice) {
            if (!$actionHandler instanceof HasVoiceMessage) {
                throw new TelegramBotException(
                    sprintf('Class %d must implement HasVoiceMessage contract', get_class($actionHandler))
                );
            }

            return $actionHandler->handleVoiceMessage($update->message->voice);
        }

        return $actionHandler->handleMessage($update->message->text);
    }

    /**
     * @throws \Throwable
     */
    private function handleCallbackQuery(Update $update): ?Response
    {
        try {
            $callbackQuery = json_decode($update->callbackQuery->data, true);

            $action = config('telegram-bot.action-enum')::from((int)$callbackQuery['action']);

            $this->telegramUser->action = $action;

            $actionHandler = $this->actionHandlerFactory->create($this->telegramUser, $this);

            if (!$actionHandler->authorize()) {
                $this->responseSender->answerCallbackQuery([
                    'callback_query_id' => $update->callbackQuery->id,
                    'show_alert' => true,
                    'text' => 'Access denied'
                ]);

                return null;
            }

            if (!$actionHandler instanceof HasCallbackQuery) {
                throw new TelegramBotException(
                    sprintf('Class %s must implement HasCallbackQuery contract', get_class($actionHandler))
                );
            }

            $this->telegramUser->save();

            $response = $actionHandler->handleCallbackQuery($action, $callbackQuery ?? []);

            if ($this->telegramUser->message_id !== $update->callbackQuery->message->messageId) {
                if ($this->telegramUser->message_id) {
                    $this->responseSender->deleteMessage($this->telegramUser, $this->telegramUser->message_id);
                }

                $this->telegramUser->message_id = null;
            }

            return $response;
        } catch (\Throwable $e) {
            $this->responseSender->answerCallbackQuery([
                'callback_query_id' => $update->callbackQuery->id,
                'show_alert' => true,
                'text' => 'Something went wrong'
            ]);

            if (!app()->isProduction()) {
                $exceptionMessage = sprintf(
                    implode("\n", [
                        '<b>File:</b> %s',
                        '<b>Line:</b> %s',
                        '<b>Message:</b> %s',
                        '<b>Code:</b> %s'
                    ]),
                    $e->getFile(),
                    $e->getLine(),
                    $e->getMessage(),
                    $e->getCode(),
                );

                $this->responseSender->sendRaw([
                    'chat_id' => $this->telegramUser->id,
                    'text' => $exceptionMessage,
                    'parse_mode' => 'HTML',
                    'disable_notification' => true,
                ]);
            }

            throw $e;
        }
    }

    public function deleteMainMenuMessage(TelegramUser $telegramUser): void
    {
        if (!$telegramUser->message_id) {
            return;
        }

        $this->responseSender->deleteMessage($telegramUser, $telegramUser->message_id);

        $telegramUser->message_id = null;
        $telegramUser->save();
    }

    public function updateMainMessage(TelegramUser $telegramUser, BackedEnum $action): void
    {
        $telegramUser->action = $action;
        $telegramUser->save();

        $this->telegramUser = $telegramUser;

        $actionHandler = $this->actionHandlerFactory->create($telegramUser, $this);
        $response = $actionHandler->buildPreviewMessage();

        $this->responseSender->send($this->telegramUser, $response);
    }

    public function sendNewMainMessage(TelegramUser $telegramUser, BackedEnum $action): void
    {
        $this->deleteMainMenuMessage($telegramUser);

        $telegramUser->action = $action;
        $telegramUser->save();

        $this->telegramUser = $telegramUser;

        $actionHandler = $this->actionHandlerFactory->create($telegramUser, $this);
        $response = $actionHandler->buildPreviewMessage();

        $this->responseSender->send($this->telegramUser, $response);
    }
}