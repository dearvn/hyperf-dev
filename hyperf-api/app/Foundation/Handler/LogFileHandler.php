<?php

declare(strict_types=1);

namespace App\Foundation\Handler;

use Monolog\Handler\RotatingFileHandler;
use Monolog\Logger;

/**
 * LogFileHandler
 * Log processing, storage file
 *
 * @package Core\Common\Handler
 */

/**
 * Log processing, storage file
 * Stay in types such as INFO, Warning, NOTIC and other types, and store a file, and ERROR type storage a file
 * Realize the date rotation by inheriting RotatingFilehandler
 * Class LogFileHandler
 * @package App\Foundation\Handler
 * @Author YiYuan-Lin
 * @Date: 2020/9/19
 */
class LogFileHandler extends RotatingFileHandler
{

    /**
     * Repair the parent method, increase judgment log output, framework log
     * @param array $record
     * @return bool
     */
    public function handle(array $record): bool
    {
        if (!$this->isHandling($record)) {
            return false;
        }
        $record = $this->processRecord($record);
        // Determine whether to start a log record
        if ( !config('app_log') ) {
            return false;
        }
        // Determine whether to deal with the framework log
        if ( !config('hf_log') && $record['channel'] == 'hyperf' ) {
            return false;
        }
        // Judgment system allows log type
        if ( ! isStdoutLog($record['level_name']) ) {
            return false;
        }
        $record['formatted'] = $this->getFormatter()->format($record);
        $this->write($record);
        return false === $this->bubble;
    }

    /**
     * Rewilling this method to change the method of storage files of the log.
     * DEBUG, ERROR, separate storage, and other rules in accordance with the original rules
     * @param array $record
     * @return bool
     */
    public function isHandling(array $record) : bool
    {
        switch ($record['level']) {
            case Logger::DEBUG:
                return $record['level'] == $this->level;
                break;
            case $record['level'] == Logger::ERROR || $record['level'] == Logger::CRITICAL || $record['level'] == Logger::ALERT || $record['level'] == Logger::EMERGENCY:
                return Logger::ERROR <= $this->level && Logger::EMERGENCY >= $this->level;
                break;
            default:
                return Logger::INFO <= $this->level && Logger::WARNING >= $this->level;
        }
    }
}