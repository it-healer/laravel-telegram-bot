<?php

namespace ItHealer\Telegram;

use ItHealer\Telegram\DTO\Message;
use ItHealer\Telegram\Foundation\RedisCollection;
use ItHealer\Telegram\Models\TelegramChat;

/**
 * @extends RedisCollection<Message>
 */
class MessageStack extends RedisCollection
{
    public function __construct(TelegramChat $chat)
    {
        parent::__construct(
            redisKey: get_class($chat).'::'.$chat->getKey(),
            getter: fn (mixed $value) => Message::fromArray(json_decode($value, true)),
            setter: fn (Message $value) => json_encode($value->toArray()),
        );
    }
}
