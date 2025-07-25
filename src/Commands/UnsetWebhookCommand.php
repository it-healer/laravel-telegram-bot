<?php

namespace ItHealer\Telegram\Commands;

use Illuminate\Console\Command;
use ItHealer\Telegram\API;
use ItHealer\Telegram\Facades\Telegram;
use ItHealer\Telegram\Models\TelegramBot;

class UnsetWebhookCommand extends Command
{
    protected $signature = 'telegram:unset-webhook';

    protected $description = 'Unset Webhook for Telegram Bot';

    public function handle(): void
    {
        /** @var class-string<TelegramBot> $model */
        $model = Telegram::botModel();

        $telegramBots = $model::get();
        if ($telegramBots->count() === 0) {
            $this->error('First register the Telegram bot using the command: php artisan telegram:new-bot');
            return;
        }

        $username = $this->choice('Which telegram bot do you want to unset?', $telegramBots->pluck('username')->all());

        $telegramBot = $telegramBots->where('username', $username)->firstOrFail();

        try {
            Telegram::setWebhook($telegramBot);

            $this->info("Webhook successfully unset for Telegram Bot @{$telegramBot->username}!");
        } catch (\Exception $e) {
            $this->error($e->getMessage());
        }
    }
}
