<?php
declare(strict_types=1);

namespace App\Foundation\Facades;

use Hyperf\Config\Annotation\Value;
use Hyperf\Logger\Logger;
use Hyperf\Utils\ApplicationContext;

/**
 * Log tool
 * Class Log
 * @Author YiYuan-Lin
 * @Date: 2020/9/19
 */
class Log
{

    /**
     * Log channel
     * @param string $group
     * @param string $name
     * @return \Psr\Log\LoggerInterface
     */
    public static function channel(string $group = 'default', string $name = 'app')
    {
        return ApplicationContext::getContainer()->get(\Hyperf\Logger\LoggerFactory::class)->get($name, $group);
    }

    /**
     * debug debug logs
     * @return \Psr\Log\LoggerInterface
     */
    public static function codeDebug()
    {
        return self::channel('code_debug', config('app_env', 'app'));
    }

    /**
     * Interface request log
     * @return \Psr\Log\LoggerInterface
     */
    public static function requestLog()
    {
        return self::channel('request_log', config('app_env', 'app'));
    }

    /**
     * The interface returns the log
     * @return \Psr\Log\LoggerInterface
     */
    public static function responseLog()
    {
        return self::channel('response_log', config('app_env', 'app'));
    }

    /**
     * SQL Record Log
     * @return \Psr\Log\LoggerInterface
     */
    public static function sqlLog()
    {
        return self::channel('sql_log', config('app_env', 'app'));
    }

    /**
     * Queen wrong log
     * @return \Psr\Log\LoggerInterface
     */
    public static function jobLog()
    {
        return self::channel('job_log', config('app_env', 'app'));
    }

    /**
     * Timing task error log
     * @return \Psr\Log\LoggerInterface
     */
    public static function crontabLog()
    {
        return self::channel('crontab_log', config('app_env', 'app'));
    }
}