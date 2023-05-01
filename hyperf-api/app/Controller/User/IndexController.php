<?php

declare(strict_types=1);

namespace App\Controller\User;

use App\Controller\AbstractController;
use Hyperf\HttpServer\Annotation\AutoController;

/**
 * Test the controller, which is generally used to test some code
 * Class IndexController
 * @AutoController()
 */
class IndexController extends AbstractController
{
    public function __construct()
    {

    }

    /**
     * Get user data list
     */
    public function index()
    {
        return 123;
    }

    public function user()
    {
        return 1;
    }
}
