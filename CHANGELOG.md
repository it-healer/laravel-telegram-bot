# Changelog

All notable changes to `laravel-telegram-bot` will be documented in this file.

## v1.3.0 - 2026-06-17

- Add support for colored keyboard buttons (Telegram Bot API 9.4): the `style` attribute on inline/reply `<column>` tags and `setStyle()` on the button DTOs (`primary`, `success`, `danger`). Invalid values throw an `InvalidArgumentException`.
- Add support for custom-emoji button icons (`icon_custom_emoji_id`): the `icon` attribute on `<column>` and `setIconCustomEmojiId()` on the button DTOs.
- Greatly expand the README: document the `<screen>` tag, media messages, reply keyboards, the full inline-keyboard attribute reference, the `telegram` auth guard, ChatAPI methods, console commands and config options.

## v1.2.0 - 2026-06-17

- Remove the `danog/telegram-entities` dependency and integrate the required entity-to-HTML code directly into the package under `ItHealer\Telegram\Support\Entities` (Apache-2.0, original copyright preserved). This drops the transitive `webmozart/assert ^1.11` constraint that conflicted with projects using `webmozart/assert ^2`.
- Add `symfony/polyfill-mbstring` to ensure `mb_*` functions are available.

## v1.1.0 - 2026-06-17

- Add support for Laravel 13 (`illuminate/contracts` and `illuminate/support` now allow `^13.0`).
- Widen dev dependencies to support Laravel 13 / PHP 8.4: `orchestra/testbench` (`^10.0`, `^11.0`) and Pest 3 (`pestphp/pest`, `pest-plugin-arch`, `pest-plugin-laravel`).
