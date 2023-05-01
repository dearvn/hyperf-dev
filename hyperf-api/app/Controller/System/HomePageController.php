<?php

declare(strict_types=1);

namespace App\Controller\System;

use App\Controller\AbstractController;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\Middleware;
use Hyperf\HttpServer\Annotation\RequestMapping;
use App\Middleware\RequestMiddleware;

/**
 * Homepage data controller
 * Class IndexController
 * @Controller(prefix="common")
 */
class HomePageController extends AbstractController
{
    /**
     * Get homepage data
     * @RequestMapping(path="home_data", methods="get")
     * @Middleware(RequestMiddleware::class)
     */
    public function index()
    {
        return $this->success();
    }
}
