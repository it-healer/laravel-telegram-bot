<?php

namespace ItHealer\Telegram\Commands;

use Illuminate\Console\Command;
use ItHealer\Telegram\Facades\Telegram;
use ItHealer\Telegram\Models\TelegramBot;

class InitCommand extends Command
{
    protected $signature = 'telegram:init';

    protected $description = 'Init Telegram Bot';

    public function handle(): void
    {
        /** @var class-string<TelegramBot> $model */
        $model = Telegram::botModel();

        $telegramBots = $model::get();
        if ($telegramBots->count() === 0) {
            $this->error('First register the Telegram bot using the command: php artisan telegram:new-bot');
            return;
        }

        $username = $this->choice('Which telegram bot do you want to init?', $telegramBots->pluck('username')->all());

        $telegramBot = $telegramBots->where('username', $username)->firstOrFail();

        try {
            Telegram::init($telegramBot);

            $this->info("Telegram Bot @{$telegramBot->username} successfully init!");
        } catch (\Exception $e) {
            $this->error($e->getMessage());
        }
    }
}
