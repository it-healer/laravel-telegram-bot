# Laravel Telegram Bot

[![Latest Version on Packagist](https://img.shields.io/packagist/v/it-healer/laravel-telegram-bot.svg?style=flat-square)](https://packagist.org/packages/it-healer/laravel-telegram-bot)
[![Total Downloads](https://img.shields.io/packagist/dt/it-healer/laravel-telegram-bot.svg?style=flat-square)](https://packagist.org/packages/it-healer/laravel-telegram-bot)

This package for Laravel 11+ allows you to easily create interactive Telegram bots, using Laravel routing, and using Blade templates to conduct a dialogue with the user.

Supports Laravel 11, 12 and 13, PHP 8.3, 8.4 and 8.5.

## Installation

You can install the package via composer:

```bash
composer require it-healer/laravel-telegram-bot
```

```bash
php artisan telegram:install
```

You can publish and run the migrations with:

```bash
php artisan vendor:publish --tag="telegram-migrations"
php artisan migrate
```

You can publish the config file with:

```bash
php artisan vendor:publish --tag="telegram-config"
```

Optionally, you can publish the views using:

```bash
php artisan vendor:publish --tag="telegram-views"
```

Optionally, if you use Sail for local development, you need add PHP params `PHP_CLI_SERVER_WORKERS="10"` in file `supervisord.conf`:
```bash
[program:php]
command=%(ENV_SUPERVISOR_PHP_COMMAND)s
user=%(ENV_SUPERVISOR_PHP_USER)s
environment=LARAVEL_SAIL="1",PHP_CLI_SERVER_WORKERS="10"
stdout_logfile=/dev/stdout
stdout_logfile_maxbytes=0
stderr_logfile=/dev/stderr
stderr_logfile_maxbytes=0
```

## Authentication

You can use Laravel Auth, edit file `config/auth.php` and edit section `guards`:
```php
'guards' => [
        'web' => [...],
        'telegram' => [
            'driver' => 'telegram',
            'provider' => 'users',
        ]
    ],
```

After this you can use middleware `auth:telegram` (and `guest:telegram`) in your routes.

The `telegram` guard is a stateful guard that links a Telegram chat to your authenticatable model through the `telegram_users` table. Use it like any other guard:

```php
// Log in by credentials (any column except password, e.g. email or login):
Auth::guard('telegram')->attempt(['login' => $login, 'password' => $password]);

// Log in a known user instance:
Auth::guard('telegram')->login($user);

// Current user / logout:
Auth::guard('telegram')->user();
Auth::guard('telegram')->logout();
```

When `auth:telegram` blocks a guest, it redirects to the route named `telegram.user.auth`. When `guest:telegram` blocks an authenticated user, it redirects to the first existing route among `telegram.dashboard`, `telegram.home`, `telegram.index`.

## Scheduling (optional)

If you want to work with automatic truncate of dialogs, run the command `php artisan telegram:truncate` every minute using Schedule.

To live-refresh pages, register the `telegram.live` middleware alias:

```php
->withMiddleware(function (Middleware $middleware) {
    $middleware->alias([
        'telegram.live' => \ItHealer\Telegram\Middleware\LiveMiddleware::class,
    ]);
})
```

Then use it in routes (the argument is the refresh frequency in seconds):
```php
Route::telegram('/', [\App\Telegram\Controllers\MyController::class, 'index'])
    ->middleware(['telegram.live:30']);
```

In file `routes/console.php` add:
```php
Schedule::command('telegram:live')->runInBackground()->everyMinute();
Schedule::command('telegram:truncate')->everyMinute();
```

## Bot management commands

```bash
php artisan telegram:new-bot          # Register a new bot (asks for the token)
php artisan telegram:init             # Apply bot settings (name, description, commands) from config
php artisan telegram:set-webhook      # Set the webhook for a bot
php artisan telegram:unset-webhook    # Remove the webhook
php artisan telegram:pooling [BOT_ID] # Manual long-polling (useful on localhost)
php artisan telegram:truncate         # Clean up old dialog messages
php artisan telegram:live             # Process live-refreshing pages
```

## Routing

Bot screens are defined in `routes/telegram.php` with the `Route::telegram()` macro. Routes are automatically named with a `telegram.` prefix.

```php
use Illuminate\Support\Facades\Route;
use ItHealer\Telegram\TelegramRequest;

Route::telegram('/', function (TelegramRequest $request) {
    return '<message><line>You wrote: '.$request->text().'</line></message>';
})->name('home');

Route::telegram('/profile', [\App\Telegram\Controllers\ProfileController::class, 'index'])
    ->name('profile')
    ->middleware('auth:telegram');
```

A handler may return a string of bot HTML, a `view(...)`, or a `redirect(...)` — redirects are followed internally and re-dispatched as another bot screen.

The `TelegramRequest` exposes the incoming update: `text()`, `post($key)`, `query()`, `chat()`, `user()`, etc.

## Messages and formatting

Bot output is written as HTML. The top-level tags are `<message>`, media tags (see below), and `<screen>`.

```html
<message>
    <line>First line</line>
    <line><b>Bold</b>, <i>italic</i>, <u>underline</u>, <s>strike</s>, <code>code</code>, <a href="https://example.com">link</a></line>
    <lines>Multi-line / pre-formatted block kept as-is</lines>
</message>
```

- `<line>` — a single line (newlines inside are stripped).
- `<lines>` — a block whose inner HTML is preserved as-is.
- Inside lines you can use Telegram HTML formatting tags (`<b>`, `<i>`, `<u>`, `<s>`, `<code>`, `<pre>`, `<a>`, `<tg-spoiler>`, …). Messages are sent with `parse_mode=html`.

### The `<screen>` tag

By default, messages are **appended** to the dialog ("classic" mode). When you wrap your output in `<screen>`, the bot **clears the current dialog and redraws everything inside from scratch** — useful for single-screen, app-like navigation where each step replaces the previous one.

```html
<screen>
    <message>
        <line>🏠 <b>Main menu</b></line>
        <inline-keyboard>
            <row>
                <column data-redirect="/profile">Profile</column>
            </row>
        </inline-keyboard>
    </message>
</screen>
```

You can place several messages/media inside one `<screen>`. Mixing `<screen>` and top-level messages produces a "mixed" render (the screen part is redrawn, the rest is appended).

## Media messages

Send media by using a media tag with a `src` attribute (a local file path, a URL, or a Telegram `file_id`). Captions are written with `<line>`/`<lines>` inside the tag.

```html
<photo src="/path/or/url/or/file_id">
    <line>Caption with <b>formatting</b></line>
</photo>

<document src="..."><line>A file</line></document>
<video src="..." show_caption_above_media="true"><line>A video</line></video>
<voice src="..."></voice>
<video-note src="..."></video-note>
```

- `src` — local path, URL or `file_id`. Uploaded files are cached by content hash, so the same file is only uploaded once and reused by `file_id`.
- `show_caption_above_media="true"` — show the caption above the media.
- `reply-message-id="123"` — reply to a specific message (works on `<message>` and media tags).

## Inline keyboards

```html
<inline-keyboard>
    <row>
        <column query-param="value">Change a query param</column>
        <column data-field="value">Send POST data</column>
    </row>
    <row>
        <column data-field="long value" encode="true">Encoded POST data</column>
        <column data-redirect="/profile">Redirect to a screen</column>
    </row>
    <row>
        <column url="https://example.com">Open a URL</column>
        <column web_app="https://example.com/app">Open a Web App</column>
    </row>
</inline-keyboard>
```

Supported `<column>` attributes:

| Attribute | Description |
| --- | --- |
| `query-*` | Set/override a query string parameter on the current screen. |
| `data-*` | Send POST data to the handler (`$request->post('field')`). |
| `encode="true"` | Encode long POST data via cache (default `true` when `data-redirect` is set). |
| `data-redirect="/uri"` | Navigate (redirect) to another screen. |
| `data-current="field"` | Edit-form: switch the current field (see Edit Form). |
| `data-submit="true"` | Edit-form: submit the form. |
| `url="..."` | Open an external URL. |
| `web_app="..."` | Open a Telegram Web App. |
| `style="..."` | Button color (Bot API 9.4+): `primary` (blue), `success` (green) or `danger` (red). |
| `icon="<custom_emoji_id>"` | Show a custom emoji on the button (`icon_custom_emoji_id`, Bot API 9.4+). |

### Colored buttons (button styles)

Since Bot API 9.4 you can color inline (and reply) keyboard buttons with the `style` attribute:

```html
<inline-keyboard>
    <row>
        <column data-redirect="/login" style="primary">🔑 Log in</column>
        <column data-redirect="/register" style="success">📝 Register</column>
    </row>
    <row>
        <column data-redirect="/delete" style="danger">🗑 Delete</column>
    </row>
</inline-keyboard>
```

Allowed values: `primary`, `success`, `danger`. Omit `style` for the default look. Old Telegram clients simply ignore the field. Invalid values throw an `InvalidArgumentException`.

You can do the same from PHP on the DTO:

```php
use ItHealer\Telegram\DTO\InlineKeyboard\Button;

Button::make()
    ->setText('Delete')
    ->setCallbackData(['action' => 'delete'])
    ->setStyle('danger')
    ->setIconCustomEmojiId('5368324170671202286');
```

## Reply keyboards

```html
<message>
    <line>Choose:</line>
    <reply-keyboard resize="true" persistent="true">
        <row>
            <column request_contact="true">Share contact</column>
            <column request_location="true">Share location</column>
        </row>
        <row>
            <column web_app="https://example.com/app">Web App</column>
            <column style="primary">Styled button</column>
        </row>
    </reply-keyboard>
</message>
```

Supported `<reply-keyboard>` attributes: `resize`, `persistent`. Supported `<column>` attributes: `request_contact`, `request_location`, `web_app`, `style`, `icon`.

## Edit Form

```php
class MyForm extends \ItHealer\Telegram\EditForm\BaseForm 
{
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'min:5', 'max:255'],
            'phone' => ['required', 'string', 'min:10', 'max:15'],
        ];
    }
    
    public function titles(): array
    {
        return [
            'name' => 'Your name',
            'phone' => 'Your phone number'
        ];
    }
}
```

```php
class MyController 
{
    public function edit(MyForm $form): mixed
    {
        $form->setDefault([
            'name' => 'Default name',
            'phone' => '1234567890',
        ]);
        
        if( $form->validate() ) {
            // $form->get();
        }
        
        return view('...', compact('form'));
    }
    
    public function create(MyForm $form): mixed
    {
        if( $form->isCreate()->validate() ) {
            // $form->get();
        }
        
        return view('...', compact('form'));
    }
}
```

```html
<message>
    <x-telegram-edit-form :form="$form">
        <x-slot:name>
            <line>Please, enter your First Name:</line>
        </x-slot:name>
    </x-telegram-edit-form>
</message>
```

`isCreate()` walks through the fields one by one (asking each in turn) and returns `true` on submit; without it the form lets the user edit any field. Use `optional()` to allow empty values (the user can type `/empty`).

## Sending and editing messages from code

Use `ChatAPI` (e.g. via `$telegramChat->api()`):

```php
$api = $chat->api();

$api->send($message);                 // send a Message DTO (text/photo/video/document/voice/...)
$api->edit($oldMessage, $newMessage); // edit an existing message
$api->delete($message);               // delete message(s)
$api->deleteMessages($id1, $id2);     // delete by message id(s)
$api->sendChatAction(ChatAction::Typing);
$api->getUserProfilePhotos();
```

The bot-level `API` (via `$bot->api()`) provides `getMe()` and `getFileLink($file)`.

## Configuration

Key options in `config/telegram.php`:

- `reactions` — map incoming commands/emoji (e.g. `/start`, `🏠`, `/back`, `/refresh`) to navigation actions.
- `screen.ttl` / `screen.truncate` — lifetime of dialog messages and auto-truncate window.
- `page.timeout` / `page.wait` / `page.delay` / `page.max_redirects` — request handling tuning.
- `cache.ttl` / `cache.encode_ttl` — TTL for cached `file_id`s and encoded callback payloads.
- `models.bot` / `models.chat` / `models.user` — override the package's Eloquent models.
- `webhook.background` — handle webhook updates in the background.
- `init` — default bot name/description/commands applied by `telegram:init`.

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Credits

- [IT-HEALER](https://github.com/it-healer)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
