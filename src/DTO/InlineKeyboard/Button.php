<?php

namespace ItHealer\Telegram\DTO\InlineKeyboard;

use ItHealer\Telegram\Abstract\DTO;

class Button extends DTO
{
    public function required(): array
    {
        return ['text', 'callback_data'];
    }

    public function text(): string
    {
        return $this->getOrFail('text');
    }

    public function setText(string $text): static
    {
        $this->attributes['text'] = $text;

        return $this;
    }

    public function url(): ?string
    {
        return $this->get('url');
    }

    public function setUrl(string $url): static
    {
        $this->attributes['url'] = $url;

        return $this;
    }

    public function callbackData(): ?string
    {
        return $this->get('callback_data');
    }

    public function setCallbackData(array $callbackData): static
    {
        $this->attributes['callback_data'] = http_build_query($callbackData);

        return $this;
    }

    public function webApp(): ?array
    {
        return $this->attributes['web_app'] ?? null;
    }

    public function setWebApp(?array $webApp): static
    {
        $this->attributes['web_app'] = $webApp;

        return $this;
    }
}
