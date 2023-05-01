<?php
declare(strict_types=1);

/**
 * Route Control Center
 */
use Hyperf\HttpServer\Router\Router;
use App\Middleware\WsMiddleware;

Router::addServer('ws', function () {
    Router::get('/', 'App\Controller\Laboratory\Ws\WebsocketController', [
        'middleware' => [WsMiddleware::class]
    ]);
});