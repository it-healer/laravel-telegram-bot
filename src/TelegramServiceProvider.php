<?php

namespace ItHealer\Telegram;

use ItHealer\Telegram\Commands\InitCommand;
use ItHealer\Telegram\Commands\LiveCommand;
use ItHealer\Telegram\Commands\NewBotCommand;
use ItHealer\Telegram\Commands\PoolingCommand;
use ItHealer\Telegram\Commands\SetWebhookCommand;
use ItHealer\Telegram\Commands\TruncateCommand;
use ItHealer\Telegram\Commands\UnsetWebhookCommand;
use ItHealer\Telegram\Components\EditForm;
use ItHealer\Telegram\Providers\AuthServiceProvider;
use ItHealer\Telegram\Providers\RouteServiceProvider;
use Spatie\LaravelPackageTools\Commands\InstallCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class TelegramServiceProvider extends PackageServiceProvider
{
    public function boot(): static
    {
        $this->app->register(RouteServiceProvider::class);
        $this->app->register(AuthServiceProvider::class);

        return parent::boot();
    }

    public function configurePackage(Package $package): void
    {
        $package
            ->name('telegram')
            ->hasConfigFile('telegram')
            ->hasViews('telegram')
            ->hasRoutes('api')
            ->hasMigrations([
                'create_telegram_bots_table',
                'create_telegram_chats_table',
                'create_telegram_users_table',
                'create_telegram_attachments_table',
            ])
            ->hasCommands([
                NewBotCommand::class,
                InitCommand::class,
                SetWebhookCommand::class,
                UnsetWebhookCommand::class,
                PoolingCommand::class,
                TruncateCommand::class,
                LiveCommand::class,
            ])
            ->hasViewComponent('telegram', EditForm::class)
            ->hasInstallCommand(function(InstallCommand $command) {
                $command
                    ->publishConfigFile()
                    ->publishMigrations()
                    ->askToRunMigrations()
                    ->publish('routes');
            });

        $this->publishes([
            $this->package->basePath("../stubs/routes/telegram.php.stub") => base_path('routes/telegram.php'),
        ], "{$this->package->shortName()}-routes");

        $this->loadViewsFrom(resource_path('views/telegram'), 'telegram');

        $this->app->register(RouteServiceProvider::class);
        $this->app->register(AuthServiceProvider::class);
    }
}
