<?php

namespace ItHealer\Telegram\Commands;

use Illuminate\Console\Command;
use ItHealer\Telegram\API;
use ItHealer\Telegram\Facades\Telegram;
use ItHealer\Telegram\Models\TelegramBot;

class NewBotCommand extends Command
{
    protected $signature = 'telegram:new-bot';

    protected $description = 'Register telegram bot in system';

    public function handle(): void
    {
        $this->start();
    }

    protected function start(): void
    {
        $token = $this->ask('Please enter telegram bot token');

        try {
            $bot = Telegram::newBot($token);

            $this->info("Telegram Bot @{$bot->username} successfully added!");
        }
        catch(\Exception $e) {
            $this->error($e->getMessage());

            $this->start();
        }
    }
}
