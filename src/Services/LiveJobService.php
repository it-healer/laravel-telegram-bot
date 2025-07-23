<?php

namespace ItHealer\Telegram\Services;

use Illuminate\Support\Facades\Log;
use ItHealer\Telegram\Models\TelegramChat;

class LiveJobService
{
    protected TelegramChat $chat;

    public function run(TelegramChat $chat): void
    {
        $this->chat = $chat;

        Log::error('TEST');
    }
}
