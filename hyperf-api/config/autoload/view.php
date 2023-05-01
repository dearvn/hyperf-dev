<?php
declare(strict_types=1);

use Hyperf\View\Mode;
use Hyperf\View\Engine\BladeEngine;

return [
    //The rendering engine used
    'engine' => BladeEngine::class,
    //If not filled in, the default is Task mode, it is recommended to use Task mode
    'mode' => Mode::TASK,
    'config' => [
        //If the following folder does not exist, please create it yourself
        'view_path' => BASE_PATH . '/storage/view/',
        'cache_path' => BASE_PATH . '/runtime/view/',
    ],
];