<?php

namespace ItHealer\Telegram\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use ItHealer\Telegram\API;
use ItHealer\Telegram\Facades\Telegram;

class TelegramBot extends Model
{
    protected $fillable = [
        'token',
        'username',
        'get_me',
    ];

    protected function casts(): array
    {
        return [
            'get_me' => 'json',
        ];
    }

    public function api(): API
    {
        return new API($this->token);
    }

    public function chats(): HasMany
    {
        /** @phpstan-ignore-next-line */
        return $this->hasMany(Telegram::chatModel(), 'bot_id');
    }
}
