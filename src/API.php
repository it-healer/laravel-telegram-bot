<?php

namespace ItHealer\Telegram;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;
use ItHealer\Telegram\Abstract\ApiClient;
use ItHealer\Telegram\DTO\BotCommand;
use ItHealer\Telegram\DTO\CallbackQuery;
use ItHealer\Telegram\DTO\Update;
use ItHealer\Telegram\DTO\User;
use ItHealer\Telegram\Interfaces\IsFile;

class API extends ApiClient
{
    protected readonly string $token;

    public function __construct(string $token)
    {
        $this->token = $token;

        $baseUri = config('telegram.api.base_uri', 'https://api.telegram.org');

        $client = Http::baseUrl("$baseUri/bot$token/")
            ->connectTimeout(config('telegram.api.connect_timeout', 20))
            ->timeout(config('telegram.api.timeout', 60));

        return parent::__construct($client);
    }

    /**
     * Возвращает информацию о боте.
     */
    public function getMe(): User
    {
        return User::fromArray(
            $this->sendRequest('getMe')
        );
    }

    /**
     * Установка WebHook для Updates.
     */
    public function setWebhook(string $url, array $options = []): bool
    {
        return $this->sendRequest('setWebhook', [
            'url' => $url,
            ...$options,
        ])[0];
    }

    /**
     * Удаление WebHook для Updates.
     */
    public function deleteWebhook(array $options = []): bool
    {
        return $this->sendRequest('deleteWebhook', $options)[0];
    }

    public function getUpdates(
        ?int $offset = null,
        ?int $limit = null,
        ?int $timeout = null,
        ?array $allowedUpdates = null
    ): array {
        $data = [];
        if (!is_null($offset)) {
            $data['offset'] = $offset;
        }
        if (!is_null($limit)) {
            $data['limit'] = $limit;
        }
        if (!is_null($timeout)) {
            $data['timeout'] = $timeout;
        }
        if (!is_null($allowedUpdates)) {
            $data['allowed_updates'] = $allowedUpdates;
        }

        $responseData = $this->sendRequest('getUpdates', $data);

        return array_map(fn(array $item) => Update::fromArray($item), $responseData);
    }

    public function setMyCommands(BotCommand|array ...$commands): bool
    {
        /** @var BotCommand[] $commands */
        $commands = Arr::flatten($commands);
        $commands = Arr::map($commands, fn(BotCommand $item) => $item->toArray());

        if (count($commands) === 0) {
            return $this->sendRequest('deleteMyCommands')[0];
        }

        return $this->sendRequest('setMyCommands', compact('commands'))[0];
    }

    public function setMyName(string $name): bool
    {
        return $this->sendRequest('setMyName', [
            'name' => $name
        ])[0];
    }

    public function setMyDescription(?string $description): bool
    {
        return $this->sendRequest('setMyDescription', [
            'description' => $description
        ])[0];
    }

    public function setMyShortDescription(?string $shortDescription): bool
    {
        return $this->sendRequest('setMyDescription', [
            'short_description' => $shortDescription
        ])[0];
    }

    public function answerCallbackQuery(CallbackQuery $callbackQuery, ?string $text = null): bool
    {
        $data = [
            'callback_query_id' => $callbackQuery->id(),
        ];
        if ($text) {
            $data['text'] = $text;
        }

        return $this->sendRequest('answerCallbackQuery', $data)[0];
    }

    public function getFileLink(IsFile $file): string
    {
        $data = $this->sendRequest('getFile', [
            'file_id' => $file->fileId(),
        ]);

        $filePath = $data['file_path'] ?? null;
        if (!$filePath) {
            throw new \Exception(print_r($data, true));
        }

        if( mb_substr($filePath, 0, 1) === '/' ) {
            return $filePath;
        }

        $baseUri = config('telegram.api.base_uri', 'https://api.telegram.org');

        return $baseUri.'/file/bot'.$this->token.'/'.$filePath;
    }
}
