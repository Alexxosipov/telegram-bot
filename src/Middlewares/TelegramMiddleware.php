<?php

namespace Alexxosipov\TelegramBot\Middlewares;

use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class TelegramMiddleware
{
    public function handle(Request $request, \Closure $next): Response
    {
        if ($request->header('x-telegram-bot-api-secret-token') !== config('telegram-bot.secret')) {
            return response()->json();
        }

        return $next($request);
    }
}