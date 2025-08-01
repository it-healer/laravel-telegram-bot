<?php

namespace ItHealer\Telegram\DTO;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Date;
use ItHealer\Telegram\Abstract\DTO;
use ItHealer\Telegram\DTO\Message\Document;
use ItHealer\Telegram\DTO\Message\Photo;
use ItHealer\Telegram\DTO\Message\Video;
use ItHealer\Telegram\DTO\Message\VideoNote;
use ItHealer\Telegram\DTO\Message\Voice;
use ItHealer\Telegram\Enums\Direction;

class Message extends DTO
{
    public static array $types = [
        'message' => Message::class,
        'photo' => Photo::class,
        'video' => Video::class,
        'document' => Document::class,
        'voice' => Voice::class,
        'video_note' => VideoNote::class,
    ];

    protected function required(): array
    {
        return [
            'message_id',
            'date',
            'chat',
        ];
    }

    public function id(): int
    {
        return (int)$this->getOrFail('message_id');
    }

    public function from(): ?User
    {
        $from = $this->get('from');

        return $from ? User::fromArray($from) : null;
    }

    public function senderChat(): ?Chat
    {
        $senderChat = $this->get('sender_chat');

        return $senderChat ? Chat::fromArray($senderChat) : null;
    }

    public function direction(): Direction
    {
        $from = $this->get('from');
        if ($from) {
            $from = User::fromArray($from);
            if (!$from->isBot()) {
                return Direction::IN;
            }
        }

        return Direction::OUT;
    }

    public function date(): Carbon
    {
        return Date::createFromTimestampUTC(
            $this->getOrFail('date')
        );
    }

    public function chat(): Chat
    {
        return Chat::fromArray(
            $this->getOrFail('chat')
        );
    }

    public function text(): ?string
    {
        return $this->get('text');
    }

    public function entities(): ?array
    {
        return $this->get('entities');
    }

    public function setText(?string $text): static
    {
        $this->attributes['text'] = $text;

        return $this;
    }

    public function inlineKeyboard(): ?InlineKeyboard
    {
        return $this->get('reply_markup.inline_keyboard') ? InlineKeyboard::fromArray(
            $this->get('reply_markup')
        ) : null;
    }

    public function setInlineKeyboard(?InlineKeyboard $inlineKeyboard): static
    {
        $this->attributes['reply_markup'] = $inlineKeyboard->toArray();

        return $this;
    }

    public function replyParameters(): ?ReplyParameters
    {
        if( $replyMessageId = $this->get('message_thread_id') ) {
            return ReplyParameters::fromArray([
                'message_id' => $replyMessageId,
            ]);
        }

        return $this->get('reply_parameters') ? ReplyParameters::fromArray(
            $this->get('reply_parameters')
        ) : null;
    }

    public function setReplyParameters(?ReplyParameters $replyParameters): static
    {
        $this->attributes['reply_parameters'] = $replyParameters->toArray();

        return $this;
    }

    public function replyKeyboard(): ?ReplyKeyboard
    {
        return $this->get('reply_markup.keyboard') ? ReplyKeyboard::fromArray(
            $this->get('reply_markup')
        ) : null;
    }

    public function setReplyKeyboard(?ReplyKeyboard $replyKeyboard): static
    {
        $this->attributes['reply_markup'] = $replyKeyboard->toArray();

        return $this;
    }

    public function replyMarkupSignature(): string
    {
        return hash('sha256', json_encode($this->get('reply_markup')));
    }

    public function contact(): ?Contact
    {
        $contact = $this->get('contact');

        return $contact ? Contact::fromArray($contact) : null;
    }

    public function webAppData(): ?WebAppData
    {
        $webAppData = $this->get('web_app_data');

        return $webAppData ? WebAppData::fromArray($webAppData) : null;
    }

    public function signature(): string
    {
        return hash('sha256', json_encode([
            'contact' => $this->get('contact'),
            'text' => preg_replace('/\s+/', '', strip_tags($this->get('text'))),
            'reply_markup' => $this->get('reply_markup'),
        ]));
    }

    public static function fromArray(array $attributes): static
    {
        if (!isset($attributes['type'])) {
            $attributes['type'] = 'message';
        }
        if (isset($attributes['photo']) || isset($attributes['photo_src'])) {
            $attributes['type'] = 'photo';
        }
        if (isset($attributes['video']) || isset($attributes['video_src'])) {
            $attributes['type'] = 'video';
        }
        if (isset($attributes['document']) || isset($attributes['document_src'])) {
            $attributes['type'] = 'document';
        }
        if (isset($attributes['voice']) || isset($attributes['voice_src'])) {
            $attributes['type'] = 'voice';
        }
        if (isset($attributes['video_note']) || isset($attributes['video_note_src'])) {
            $attributes['type'] = 'video_note';
        }

        return new self::$types[$attributes['type']]($attributes, true);
    }
}
