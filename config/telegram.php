<?php

use ItHealer\Telegram\DTO\BotCommand;

return [
    'api' => [
        'base_uri' => env('TELEGRAM_BASE_URI', 'https://api.telegram.org'),
        'connect_timeout' => env('TELEGRAM_CONNECT_TIMEOUT', 20),
        'timeout' => env('TELEGRAM_TIMEOUT', 60),
    ],
    'init' => [
        'default' => [
            // 'name' => 'Название бота',
            // 'description' => 'Описание бота',
            // 'short_description' => 'Короткое описание бота',
            'commands' => [
                BotCommand::create('start', 'Главное меню'),
                BotCommand::create('refresh', 'Обновить'),
                BotCommand::create('back', 'Назад'),
            ],
        ]
    ],
    'reactions' => [
        'start' => ['/start'],
        'home' => ['🏠', 'Ⓜ️', '/home'],
        'back' => ['⬅️', '🔙', '/back'],
        'refresh' => ['🔄', '/refresh'],
        'reload' => ['/reload'],
    ],
    'callback' => [
        'start' => null,
        'back' => null,
    ],
    'page' => [
        'timeout' => 60, // таймаут в секундах на обработку запроса
        'wait' => 5, // время ожидания завершения предыдущего запроса
        'delay' => 2, // задержка после обработки запроса
        'max_redirects' => 3, // максимальное количество редиректов
    ],
    'middleware' => [ // Global Middleware

    ],
    'webhook' => [
        'background' => false,
        // запускать обработчик webhook в фоновом режиме (позволяет не создавать очередь в Telegram, требует proc_open).
    ],
    'cache' => [
        'ttl' => 86400,
        'encode_ttl' => 3 * 24 * 60 * 60,
    ],
    'screen' => [
        'ttl' => 86400, // срок жизни сообщения в диалоге
        'truncate' => 2 * 24 * 60 * 60, // очистка диалога кроме сообщения с reply keyboard через n-секунд после неактивности
    ],
    'models' => [
        'bot' => \ItHealer\Telegram\Models\TelegramBot::class,
        'chat' => \ItHealer\Telegram\Models\TelegramChat::class,
        'user' => \ItHealer\Telegram\Models\TelegramUser::class,
        'attachment' => \ItHealer\Telegram\Models\TelegramAttachment::class,
    ]
];
