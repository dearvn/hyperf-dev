<?php

declare(strict_types=1);

/**
 * Global registration middleware
 * @Author YiYuan-Lin
 * @date 2020/09/21 11:03
 */
return [
    'http' => [
        \App\Middleware\CorsMiddleware::class,
        \App\Middleware\CheckMaintainMiddleware::class,
    ],
];
