<?php

namespace Alexxosipov\Telegram\Actions;

use Alexxosipov\Telegram\Models\TelegramUser;
use Alexxosipov\Telegram\Storage\BaseStorage;
use Alexxosipov\Telegram\TelegramBot;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class ActionHandlerFactory
{
    private const CACHE_KEY = 'telegram_handler_classes';

    public static function make(TelegramUser $telegramUser, TelegramBot $telegramBot): ?ActionHandler
    {
        $handlers = static::getHandlers();

        $handlerClass = $handlers[$telegramUser->action] ?? null;

        if (!$handlerClass) {
            return null;
        }

        $storage = app()->makeWith(BaseStorage::class, [
            'user' => $telegramUser
        ]);

        return new $handlerClass($telegramUser, $telegramBot, $storage);
    }

    private static function getHandlers(): array
    {
        return Cache::remember(self::CACHE_KEY, 3600, function () {
            $handlers = [];
            $handlerDirectory = app_path('Handlers');

            foreach (File::files($handlerDirectory) as $file) {
                $class = 'App\\Handlers\\' . Str::replaceLast('.php', '', Str::of($file->getFilename())->basename());
                $action = (new \ReflectionClass($class))->getProperty('action')->getDefaultValue();
                $handlers[$action->value] = $class;
            }

            return $handlers;
        });
    }
}