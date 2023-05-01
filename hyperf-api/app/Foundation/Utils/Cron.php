<?php

namespace App\Foundation\Utils;

use App\Constants\StatusCode;
use App\Exception\Handler\BusinessException;
use Cron\CronExpression;
use Hyperf\Di\Annotation\Inject;
//use phpDocumentor\Reflection\DocBlock\Tags\Throws;

/**
 * CronTab表达式解析工具类
 * Class Cron
 * @Author YiYuan-Lin
 * @Date: 2021/4/13
 */
class Cron
{
    /**
     * cron object
     * @var
     */
    private static $cron;

    /**
     * expression
     * @var
     */
    private static $expression;

    /**
     * Instance of your own class
     * @var
     */
    private static $self;

    /**
     * Initialization expression
     * @param string $expression
     * @return Cron
     */
    public static function init(string $expression = '')
    {
        if (empty($expression))Throw new BusinessException(StatusCode::ERR_EXCEPTION, '表达式不正确');
        self::$cron = new CronExpression($expression);
        self::$self = new static;
        self::$expression = $expression;
        return self::$self;
    }

    /**
     * Get CRON next time
     * @return \DateTime
     * @throws \Exception
     */
    public static function getNextRunDate()
    {
        return self::$cron->getNextRunDate();
    }

    /**
     * Get CRON last time
     * @return \DateTime
     * @throws \Exception
     */
    public static function getPreviousRunDate()
    {
        return self::$cron->getPreviousRunDate();
    }

    /**
     * Or the execution time of the up/down X times
     * @param int $total
     * @param string $currentTime
     * @param bool $invert
     * @param bool $allowCurrentDate
     * @param null $timeZone
     * @return array|\DateTime[]
     */
    public static function getMultipleRunDates(int $total, $currentTime = 'now', bool $invert = false, bool $allowCurrentDate = false, $timeZone = null)
    {
        return self::$cron->getMultipleRunDates($total, $currentTime, $invert, $allowCurrentDate, $timeZone);
    }
}