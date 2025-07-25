<?php

namespace ItHealer\Telegram\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Process;
use ItHealer\Telegram\Facades\Telegram;
use ItHealer\Telegram\Models\TelegramBot;
use ItHealer\Telegram\Models\TelegramChat;
use ItHealer\Telegram\Services\WebhookHandler;

class WebhookController
{
    public function handle(Request $request, string $token, WebhookHandler $handler): Response
    {
        $background = config('telegram.webhook.background', false);
        if ($background) {
            $executeURL = route('telegram.execute', compact('token'));
            $cmd = 'curl -X POST '.$executeURL.' -d "'.http_build_query($request->post()).'" > /dev/null &';
            Process::run($cmd);
        } else {
            $this->execute($request, $token, $handler);
        }

        return response()->noContent();
    }

    public function execute(Request $request, string $token, WebhookHandler $handler): Response
    {
        try {
            /** @var class-string<TelegramBot> $model */
            $model = Telegram::botModel();

            $bot = $model::whereToken($token)->firstOrFail();

            $handler->handle($request, $bot);
        } catch (\Exception $e) {
            Log::error($e);
        }

        return response()->noContent();
    }

    public function live(Request $request, WebhookHandler $handler): Response
    {
        try {
            $chat = $request->post('chat');
            $chat = Crypt::decrypt($chat);

            /** @var class-string<TelegramChat> $model */
            $model = Telegram::chatModel();

            $chat = $model::findOrFail($chat);

            $handler->live($chat);
        } catch (\Exception $e) {
            Log::error($e);
        }

        return response()->noContent();
    }
}
