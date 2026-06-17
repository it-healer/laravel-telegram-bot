<?php

namespace ItHealer\Telegram\DTO\Concerns;

use InvalidArgumentException;

/**
 * Adds support for the Bot API 9.4+ button "style" (color) and
 * "icon_custom_emoji_id" fields to keyboard button DTOs.
 */
trait HasButtonStyle
{
    /**
     * Allowed button color styles (Bot API 9.4+).
     *
     * @var array<int, string>
     */
    public const STYLES = ['primary', 'success', 'danger'];

    /**
     * Visual color style of the button: "primary" (blue), "success" (green)
     * or "danger" (red). Null means the default style.
     */
    public function style(): ?string
    {
        return $this->get('style');
    }

    public function setStyle(?string $style): static
    {
        if ($style !== null && ! in_array($style, self::STYLES, true)) {
            throw new InvalidArgumentException(
                'Invalid button style "'.$style.'". Allowed styles: '.implode(', ', self::STYLES).'.'
            );
        }

        if ($style === null) {
            unset($this->attributes['style']);
        } else {
            $this->attributes['style'] = $style;
        }

        return $this;
    }

    /**
     * Custom emoji identifier to show on the button (Bot API 9.4+).
     */
    public function iconCustomEmojiId(): ?string
    {
        return $this->get('icon_custom_emoji_id');
    }

    public function setIconCustomEmojiId(?string $iconCustomEmojiId): static
    {
        if ($iconCustomEmojiId === null) {
            unset($this->attributes['icon_custom_emoji_id']);
        } else {
            $this->attributes['icon_custom_emoji_id'] = $iconCustomEmojiId;
        }

        return $this;
    }
}
