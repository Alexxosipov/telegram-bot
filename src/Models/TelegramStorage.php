<?php

declare(strict_types=1);

namespace Alexxosipov\Telegram\Models;

use Illuminate\Database\Eloquent\Model;

class TelegramStorage extends Model
{
    protected $table = 'telegram_storage';

    protected $fillable = [
        'key',
        'value',
    ];
}
