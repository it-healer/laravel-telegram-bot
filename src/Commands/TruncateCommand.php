<?php

namespace ItHealer\Telegram\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Date;
use ItHealer\Telegram\DTO\Message;
use ItHealer\Telegram\Facades\Telegram;
use ItHealer\Telegram\MessageStack;
use ItHealer\Telegram\Models\TelegramBot;
use ItHealer\Telegram\Models\TelegramChat;

class TruncateCommand extends Command
{
    protected $signature = 'telegram:truncate';

    protected $description = 'Truncate dialogs for telegram bots';

    protected int $screenTruncate;

    public function handle(): void
    {
        $this->screenTruncate = (int)config('telegram.screen.truncate', 0);

        if ($this->screenTruncate > 0) {
            /** @var class-string<TelegramChat> $model */
            $model = Telegram::chatModel();

            $model::query()
                ->with('bot')
                ->each(function (TelegramChat $chat) {
                    try {
                        if( $this->eachChat($chat) ) {
                            $this->info("Chat $chat->id successfully truncated!");
                        }
                    }
                    catch( \Exception $e ) {
                        $this->error("Chat $chat->id error: {$e->getMessage()}");
                    }
                });
        }
    }

    protected function eachChat(TelegramChat $chat): bool
    {
        $stack = new MessageStack($chat);
        if ($stack->count() > 0) {
            $mainMessage = $stack->last(fn(Message $item) => $item->replyKeyboard() !== null);

            $deleteMessages = $stack
                ->collect()
                ->filter(fn(Message $item) => $item->id() !== $mainMessage?->id() && abs(Date::now()->diffInSeconds($item->date())) >= $this->screenTruncate )
                ->map(fn(Message $item) => $item->id())
                ->filter(fn($id) => $id !== $mainMessage?->id());

            $saveMessages = $stack->collect()
                ->filter(fn(Message $item) => $item->id() === $mainMessage?->id() || abs(Date::now()->diffInSeconds($item->date())) < $this->screenTruncate );

            if( $deleteMessages->count() ) {
                try {
                    $chat->api()->deleteMessages($deleteMessages->all());
                }
                catch(\Exception $e) {}

                $stack->truncate();

                foreach( $saveMessages as $message ) {
                    $stack->push($message);
                }

                return true;
            }
        }

        return false;
    }
}
