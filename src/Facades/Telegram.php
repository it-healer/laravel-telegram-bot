<?php

namespace ItHealer\Telegram\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \ItHealer\Telegram\Telegram
 */
class Telegram extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \ItHealer\Telegram\Telegram::class;
    }
}
