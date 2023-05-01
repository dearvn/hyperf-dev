<?php

use Hyperf\Contract\StdoutLoggerInterface;

/**
 * Help function related to application logic
 * create by linyiyuan
 */
if (! function_exists('isStdoutLog')) {
    /**
     * isStdoutLog
     * Determine whether the log type allows the output
     * @param string $level
     * @return bool
     */
    function isStdoutLog(string $level)
    {
        $config = config(StdoutLoggerInterface::class, ['log_level' => []]);
        return in_array(strtolower($level), $config['log_level'], true);
    }
}

if(! function_exists('conSet')) {
    /**
     * Settlement correction context
     * @param string $id
     * @param $value
     * @return mixed
     */
    function conSet(string $id, $value) {
        return \Hyperf\Utils\Context::set($id, $value);
    }
}

if(! function_exists('conGet')) {
    /**
     * Get the context
     * @param string $id
     * @param $default
     * @return mixed
     */
    function conGet(string $id, $default = null) {
        return \Hyperf\Utils\Context::get($id, $default);
    }
}