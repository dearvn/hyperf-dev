<?php
if (!function_exists('get_os')) {
    /**
     * Get the operating system
     * @return string
     */
    function get_os() {
        $request = new \Hyperf\HttpServer\Request();
        if (!empty($request->getHeader('user-agent'))) {
            $OS = $request->getHeader('user-agent')[0];
            if (preg_match('/win/i', $OS)) {
                $OS = 'Windows';
            } elseif (preg_match('/mac/i', $OS)) {
                $OS = 'MAC';
            } elseif (preg_match('/linux/i', $OS)) {
                $OS = 'Linux';
            } elseif (preg_match('/unix/i', $OS)) {
                $OS = 'Unix';
            } elseif (preg_match('/bsd/i', $OS)) {
                $OS = 'BSD';
            } else {
                $OS = 'Other';
            }
            return $OS;
        } else {
            return "Obtaining the information of the visitors' operating system failed！";
        }
    }
}


if (!function_exists('get_browser_os')) {
    /**
     * Get the browser model
     * @return string
     */
    function get_browser_os() {
        $request = new \Hyperf\HttpServer\Request();
        if (!empty($request->getHeader('user-agent'))) {
            $br = $request->getHeader('user-agent')[0];
            if (preg_match('/MSIE/i', $br)) {
                $br = 'MSIE';
            } elseif (preg_match('/Firefox/i', $br)) {
                $br = 'Firefox';
            } elseif (preg_match('/Chrome/i', $br)) {
                $br = 'Chrome';
            } elseif (preg_match('/Safari/i', $br)) {
                $br = 'Safari';
            } elseif (preg_match('/Opera/i', $br)) {
                $br = 'Opera';
            } else {
                $br = 'Other';
            }
            return $br;
        } else {
            return "Obtaining the browser information failed！";
        }
    }
}