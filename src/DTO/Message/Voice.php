<?php

namespace ItHealer\Telegram\DTO\Message;


use Illuminate\Support\Collection;
use ItHealer\Telegram\DTO\Message;
use ItHealer\Telegram\DTO\PhotoSize;
use ItHealer\Telegram\DTO\VideoFile;
use ItHealer\Telegram\DTO\VoiceNote;
use ItHealer\Telegram\Interfaces\HasCaption;

class Voice extends Message implements HasCaption
{
    public function voiceNote(): ?VoiceNote
    {
        $value = $this->get('voice');

        return $value ? VoiceNote::fromArray($value) : null;
    }

    public function voiceSrc(): ?string
    {
        return $this->get('voice_src');
    }

    public function setVoiceSrc(string $path): static
    {
        $this->attributes['voice_src'] = $path;

        return $this;
    }

    public function caption(): ?string
    {
        return $this->get('caption');
    }

    public function captionEntities(): ?array
    {
        return $this->get('caption_entities');
    }

    public function setCaption(?string $caption): static
    {
        $this->attributes['caption'] = $caption;

        return $this;
    }

    public function captionSignature(): ?string
    {
        $caption = $this->get('caption');
        $caption = strip_tags($caption);
        $caption = preg_replace('/\s+/', '', $caption);

        return hash('sha256', $caption);
    }

    public function signature(): string
    {
        return hash('sha256', json_encode([
            'voice_src' => $this->get('voice_src'),
            'caption' => $this->captionSignature(),
            'reply_markup' => $this->get('reply_markup'),
        ]));
    }
}
