<?php

namespace ItHealer\Telegram\Middleware;

use Closure;
use Illuminate\Http\Response;
use ItHealer\Telegram\TelegramRequest;

class NotFoundMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  TelegramRequest  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(TelegramRequest $request, Closure $next): mixed
    {
        /** @var Response $response */
        $response = $next($request);

        if ($response->status() === Response::HTTP_NOT_FOUND) {
            $newContent = '<message><p>'.__('Page not found.').'</p></message>';
            $response->setContent($newContent);
            die('sex');
        }

        return $response;
    }
}
