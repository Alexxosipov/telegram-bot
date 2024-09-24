<?php

declare(strict_types=1);

namespace Alexxosipov\Telegram;

use Alexxosipov\Telegram\Actions\Contracts\HasCallbackQuery;
use Alexxosipov\Telegram\Models\TelegramUser;
use Alexxosipov\Telegram\Actions\ActionHandler;
use Alexxosipov\Telegram\Actions\Contracts\HasTextMessage;
use Alexxosipov\Telegram\Actions\ActionHandlerFactory;
use Alexxosipov\Telegram\Commands\CommandHandlerFactory;
use Alexxosipov\Telegram\Exceptions\TelegramBotException;
use Alexxosipov\Telegram\Response\Response;
use Telegram\Bot\Exceptions\TelegramResponseException;
use Telegram\Bot\Laravel\Facades\Telegram;
use Telegram\Bot\Objects\Update;
use Telegram\Bot\Objects\User;
use Illuminate\Support\Facades\Log;

final class OldTelegramBot
{
    private ?TelegramUser $telegramUser = null;

    public function __construct(
        public readonly ActionHandlerFactory $actionHandlerFactory,
        private readonly CommandHandlerFactory $commandHandlerFactory,
    ) {
    }

    public function handle(Update $update): void
    {
        $isBotChat = $update->message->chat->type === 'private';
        $isGroupUpdate = false;
        $updateType = '';
        $message = '';
        $voiceMessage = null;

        $callbackData = null;
        $user = null;

        try {
            if ($update->message) {
                $this->handleMessage($update);
            }

            if ($update->callbackQuery) {
                $this->handleCallbackQuery($update);
            }
        } catch (TelegramBotException $e) {
            report($e);
        } catch (\Throwable $e) {
            report($e);
        }
    }

    private function handleCallbackQuery(Update $update): void
    {
        $callbackQuery = json_decode($update->callbackQuery->data, true);
        $action = config('telegram-bot.action-enum')::from((int) $callbackQuery['action']);

        $handler = $this->getActionHandler($update->callbackQuery->from);

        if (! $handler instanceof HasCallbackQuery) {
            throw new TelegramBotException(
                sprintf('Class %d must implement HasCallbackQuery contract', get_class($handler))
            );
        }

        Log::debug('TG callback query debug', [
            'query' => $update->callbackQuery->data,
            'action' => $action->name,
            'user' => $this->telegramUser->id,
            'handler' => get_class($handler),
        ]);

        $this->sendResponse($handler->handleCallbackQuery($callbackQuery ?? []));
    }

    private function handleMessage(Update $update): void
    {
        if ($update->message->chat->type !== 'private') {
            return;
        }

        if ($update->message->hasCommand()) {
            $this->handleCommand($update);

            return;
        }

        $handler = $this->getActionHandler($update->message->from);

        $this->deleteMessage($update->message->messageId);

        if ( ! $handler instanceof HasTextMessage) {
            return;
        }

        $this->sendResponse($handler->handleMessage($update->message->text));
    }

    private function getActionHandler(User $user): ?ActionHandler
    {
        $this->setupUser($user);

        return $this->actionHandlerFactory->make($this->telegramUser, $this);
    }

    public function setUser(TelegramUser $telegramUser): OldTelegramBot
    {
        $this->telegramUser = $telegramUser;

        return $this;
    }

    public function getUser(): ?TelegramUser
    {
        return $this->telegramUser;
    }

    private function setupUser(User $user): void
    {
        /** @var TelegramUser $telegramUser */
        $telegramUser = TelegramUser::firstOrCreate([
            'id' => $user->id,
        ], [
            'username' => $user->username,
            'first_name' => $user->firstName,
            'last_name' => $user->lastName,
            'action' => TelegramAction::MainMenu,
        ]);

        $this->setUser($telegramUser);
    }

    public function sendResponse(?Response $response): void
    {
        if (is_null($response)) {
            return;
        }

        $message = [
            'chat_id' => $this->telegramUser->id,
            'text' => $response->text,
            'parse_mode' => 'HTML',
            'disable_notification' => true,
            "disable_web_page_preview" => true,
        ];

        if ($response->keyboard) {
            $message['reply_markup'] = $response->keyboard->toTelegramKeyboard();
        }

        if ( ! $this->telegramUser->message_id) {
            $message = Telegram::sendMessage($message);
            $this->telegramUser->update(['message_id' => $message->messageId]);

            return;
        }

        $data = array_merge($message, [
            'message_id' => $this->telegramUser->message_id,
        ]);

        try {
            Telegram::editMessageText($data);
        } catch (TelegramResponseException $e) {
            // the only way to determine error when message is not modified
            if ($e->getMessage() === 'Bad Request: message is not modified: specified new message content and reply markup are exactly the same as a current content and reply markup of the message') {
                return;
            }

            $message = Telegram::sendMessage($message);
            $this->telegramUser->update(['message_id' => $message->messageId]);
            Log::debug('Exception data', [
                $e->getErrorType(), $e->getMessage(), $e->getCode(), $e->getFile(), $e->getLine(),
            ]);
            report($e);
        }
    }

    private function handleCommand(Update $update): void
    {
        $parsedCommand = explode(' ', $update->message->text);
        $command = $parsedCommand[0];
        $params = $parsedCommand[1] ?? null;
        $debug = compact('command', 'params');
        $debug['rawCommand'] = $update->message->text;

        Log::debug('Command', $debug);

        $this->setupUser($update->message->from);

        $commandHandler = $this->commandHandlerFactory->create(
            $command,
            $this->telegramUser,
            $this,
            $params
        );

        if ($commandHandler) {
            $this->sendResponse($commandHandler->handle());
        }

        $this->deleteMessage($update->message->messageId);
    }

    public function deleteMessage(int|null $messageId): void
    {
        if ( ! $messageId) {
            return;
        }

        try {
            Telegram::deleteMessage([
                'chat_id' => $this->telegramUser->id,
                'message_id' => $messageId,
            ]);
        } catch (TelegramResponseException) {
        }
    }

    public function deleteCurrentMessage(): void
    {
        $this->deleteMessage($this->telegramUser->message_id);
    }

    public function sendNotification(string $text, ?TelegramAction $nextAction = null, ?array $data = null): void
    {
        $this->deleteCurrentMessage();

        Telegram::sendMessage([
            'chat_id' => $this->telegramUser->id,
            'text' => $text,
            'parse_mode' => 'HTML',
            "disable_web_page_preview" => true,
        ]);

        if ($nextAction) {
            $this->telegramUser->action = $nextAction;
            $this->telegramUser->save();
        }

        $action = $this->actionHandlerFactory->create($this->telegramUser, $this);

        $this->sendResponse($action->buildPreviewMessage($data));
    }
}
