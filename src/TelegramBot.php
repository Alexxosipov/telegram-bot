<?php

namespace Alexxosipov\TelegramBot;

use Alexxosipov\TelegramBot\Actions\ActionHandlerFactory;
use Alexxosipov\TelegramBot\Actions\Contracts\HasCallbackQuery;
use Alexxosipov\TelegramBot\Actions\Contracts\HasTextMessage;
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
        private readonly ResponseSenderContract $responseSender
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
            dd($e);
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

        $this->responseSender->deleteMessage($this->telegramUser, $update->message->messageId);

        if ($update->message->hasCommand()) {
            return $this->handleCommand($update);
        }

        $actionHandler = $this->actionHandlerFactory->create($this->telegramUser, $this);

        if (!$actionHandler instanceof HasTextMessage) {
            throw new TelegramBotException(
                sprintf('Class %d must implement HasCallbackQuery contract', get_class($actionHandler))
            );
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

            $this->telegramUser->update([
                'action' => $action
            ]);

            $actionHandler = $this->actionHandlerFactory->create($this->telegramUser, $this);

            if (!$actionHandler->authorize()) {
                $this->responseSender->answerCallbackQuery([
                    'callback_query_id' => $update->callbackQuery->id,
                    'show_alert' => true,
                    'text' => "Access denied."
                ]);

                return null;
            }

            if (!$actionHandler instanceof HasCallbackQuery) {
                throw new TelegramBotException(
                    sprintf('Class %s must implement HasCallbackQuery contract', get_class($actionHandler))
                );
            }

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
}