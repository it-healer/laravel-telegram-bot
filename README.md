# Laravel Telegram Bot

[![Latest Version on Packagist](https://img.shields.io/packagist/v/it-healer/laravel-telegram-bot.svg?style=flat-square)](https://packagist.org/packages/it-healer/laravel-telegram-bot)
[![Total Downloads](https://img.shields.io/packagist/dt/it-healer/laravel-telegram-bot.svg?style=flat-square)](https://packagist.org/packages/it-healer/laravel-telegram-bot)

This package for Laravel 11+ allows you to easily create interactive Telegram bots, using Laravel routing, and using Blade templates to conduct a dialogue with the user.

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

After this you can use middleware `auth:telegram` in your routes.

If you want work with automatic truncate dialogs, you must run command `php artisan telegram:truncate` every minute using Schedule.

```php
->withMiddleware(function (Middleware $middleware) {
    $middleware->alias([
        'telegram.live' => \ItHealer\Telegram\Middleware\LiveMiddleware::class,
    ]);
})
```

After you can use middleware in routes:
```php
Route::telegram('/', [\App\Telegram\Controllers\MyController::class, 'index'])
    ->middleware(['telegram.live:30']);
```

Arguments - is frequency in seconds, how often to update the page.

In file `routes/console.php` add:
```php
Schedule::command('telegram:live')
    ->runInBackground()
    ->everyMinute();
```

## Usage

Create new Telegram Bot:

```php
php artisan telegram:new-bot
```


Set Webhook for bot:

```php
php artisan telegram:set-webhook
```


Unset Webhook for bot:

```php
php artisan telegram:unset-webhook
```


Manual pooling (on localhost) for bot:

```php
php artisan telegram:pooling [BOT_ID]
```


### Inline Keyboard

If you want create button for change current URI query params, use this template:

```html
<inline-keyboard>
    <row>
        <column query-param="value">Change query param</column>
    </row>
</inline-keyboard>
```

If you want send POST data you must use this template:

```html
<inline-keyboard>
    <row>
        <column data-field="value">Send field value</column>
    </row>
</inline-keyboard>
```

If you POST data is long, you can encrypt using this template:

```html
<inline-keyboard>
    <row>
        <column data-field="long value" encode="true">Encoded send data</column>
    </row>
</inline-keyboard>
```

If you want make redirect to another page from button, use this template:

```html
<inline-keyboard>
    <row>
        <column data-redirect="/">Redirect to /</column>
    </row>
</inline-keyboard>
```

### Edit Form

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
            'name' => 'Ваше имя',
            'phone' => 'Ваш номер телефона'
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

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Credits

- [IT-HEALER](https://github.com/it-healer)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
