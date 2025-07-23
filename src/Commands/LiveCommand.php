<?php

namespace ItHealer\Telegram\Commands;

use Illuminate\Console\Command;
use ItHealer\Telegram\Services\LiveRunService;

class LiveCommand extends Command
{
    protected $signature = 'telegram:live';

    protected $description = 'Command description';

    public function handle(LiveRunService $service): void
    {
        $service
            ->setLogger(
                fn(
                    string $message,
                    string $type
                ) => $this->{$type === 'info' ? 'line' : ($type === 'success' ? 'info' : $type)}($message)
            )
            ->run();
    }
}
