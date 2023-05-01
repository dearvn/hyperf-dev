<?php

declare(strict_types=1);
/**
 * This file is part of Hyperf.
 *
 * @link     https://www.hyperf.io
 * @document https://hyperf.wiki
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */
return [
    //Register the task scheduler process
    Hyperf\Crontab\Process\CrontabDispatcherProcess::class,
    //register queue process
    Hyperf\AsyncQueue\Process\ConsumerProcess::class,
];
