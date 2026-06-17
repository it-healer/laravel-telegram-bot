# Changelog

All notable changes to `laravel-telegram-bot` will be documented in this file.

## v1.2.0 - 2026-06-17

- Remove the `danog/telegram-entities` dependency and integrate the required entity-to-HTML code directly into the package under `ItHealer\Telegram\Support\Entities` (Apache-2.0, original copyright preserved). This drops the transitive `webmozart/assert ^1.11` constraint that conflicted with projects using `webmozart/assert ^2`.
- Add `symfony/polyfill-mbstring` to ensure `mb_*` functions are available.

## v1.1.0 - 2026-06-17

- Add support for Laravel 13 (`illuminate/contracts` and `illuminate/support` now allow `^13.0`).
- Widen dev dependencies to support Laravel 13 / PHP 8.4: `orchestra/testbench` (`^10.0`, `^11.0`) and Pest 3 (`pestphp/pest`, `pest-plugin-arch`, `pest-plugin-laravel`).
