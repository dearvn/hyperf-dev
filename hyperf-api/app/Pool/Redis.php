<?php
declare(strict_types=1);

namespace App\Pool;

use App\Foundation\Traits\Singleton;
use Hyperf\Redis\Pool\PoolFactory;
use Hyperf\Redis\Redis as BaseRedis;
use Hyperf\Redis\RedisFactory;
use Hyperf\Utils\ApplicationContext;

/**
 * Default Redis library connection pool
 * Class Redis
 * @package App\Pool
 * @Author YiYuan-Lin
 * @Date: 2021/3/10
 */
class Redis
{
    /**
     * Define the name of the connection pool
     * @var
     */
    private static $connection = 'default';

    /**
     * Define single mode
     * @var
     */
    private static $instance;

    /**
     * The construction method is privatized to prevent external creation instances
     * SingletonTrait constructor.
     */
    private function __construct(){}

    /**
     * The clone method is privatized to prevent copying examples
     */
    private function __clone(){}

    /**
     * Return a redis single case
     * @param mixed ...$args
     * @return BaseRedis|mixed
     */
    static function getInstance(...$args)
    {
        if(!isset(self::$instance)){
            self::$instance =  ApplicationContext::getContainer()->get(RedisFactory::class)->get(self::$connection);
        }
        return self::$instance;
    }

}