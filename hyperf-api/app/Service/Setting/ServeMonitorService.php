<?php
namespace App\Service\Setting;

use App\Foundation\Traits\Singleton;
use App\Service\BaseService;
use Hyperf\Di\Annotation\Inject;
use Swoole\Http\Request;


/**
 * Service monitoring service
 * Class ServeMonitorService
 * @package App\Service\Setting
 * @Author YiYuan-Lin
 * @Date: 2021/2/4
 */
class ServeMonitorService extends BaseService
{
    use Singleton;

    /**
     * Get CPU information
     * @return array
     */
    public function getCpuInfo() : array
    {
        // Get CPU information
        $cpuInfo = [];
        if (false === ($str = @file("/proc/cpuinfo"))) return [];
        $str = implode("", $str);
        @preg_match_all("/model\s+name\s{0,}\:+\s{0,}([\w\s\)\(\@.-]+)([\r\n]+)/s", $str, $model);
        @preg_match_all("/cpu\s+MHz\s{0,}\:+\s{0,}([\d\.]+)[\r\n]+/", $str, $mhz);
        @preg_match_all("/cache\s+size\s{0,}\:+\s{0,}([\d\.]+\s{0,}[A-Z]+[\r\n]+)/", $str, $cache);
        @preg_match_all("/bogomips\s{0,}\:+\s{0,}([\d\.]+)[\r\n]+/", $str, $boGomIps);
        if(false !== is_array($model[1])){
            $cpuInfo['num'] = sizeof($model[1]);
            $cpuInfo['num_text'] = str_replace(array(1,2,4,8,16),array('single', 'dual', 'four', 'eight', 'sixteen'),$cpuInfo['num']).' core';
            for($i = 0; $i < $cpuInfo['num']; $i++){
                $cpuInfo['model'] = $model[1][$i].'&nbsp;('.$mhz[1][$i].')';
                $cpuInfo['mhz'] = $mhz[1][$i];
                $cpuInfo['cache'] = $cache[1][$i];
                $cpuInfo['bogomips'] = $boGomIps[1][$i];
            }
            $x1 = ($cpuInfo['num' ]== 1) ? '' : '×' . $cpuInfo['num'];
            $mhz[1][0] = '|Frequency:' . $mhz[1][0];
            $cache[1][0] = '|Two-level cache:' . $cache[1][0];
            $boGomIps[1][0] = '|Bogomips:' . $boGomIps[1][0];
            $cpuInfo['model'] = $model[1][0].$mhz[1][0] . $cache[1][0] . $boGomIps[1][0] . $x1;
            if(false !== is_array($cpuInfo['model'])) $cpuInfo['model'] = implode("<br/>", $cpuInfo['model']);
            if(false !== is_array($cpuInfo['mhz'])) $cpuInfo['mhz'] = implode("<br/>", $cpuInfo['mhz']);
            if(false !== is_array($cpuInfo['cache'])) $cpuInfo['cache'] = implode("<br/>", $cpuInfo['cache']);
            if(false !== is_array($cpuInfo['bogomips'])) $cpuInfo['bogomips'] = implode("<br/>", $cpuInfo['bogomips']);
        }
        return $cpuInfo;
    }

    /**
     * Get the server memory information
     * @return array
     */
    public function getMemoryInfo()
    {
        $memoryInfo = [];
        if(false === ($str = @file("/proc/meminfo")))return [];
        $str = implode("", $str);
        preg_match_all("/MemTotal\s{0,}\:+\s{0,}([\d\.]+).+?MemFree\s{0,}\:+\s{0,}([\d\.]+).+?Cached\s{0,}\:+\s{0,}([\d\.]+).+?SwapTotal\s{0,}\:+\s{0,}([\d\.]+).+?SwapFree\s{0,}\:+\s{0,}([\d\.]+)/s", $str,$buf);
        preg_match_all("/Buffers\s{0,}\:+\s{0,}([\d\.]+)/s",$str, $buffers);
        $memoryInfo['mem_total'] = round($buf[1][0]/1024,2);
        $memoryInfo['mem_free'] = round($buf[2][0]/1024,2);
        $memoryInfo['mem_buffers'] = round($buffers[1][0]/1024,2);
        $memoryInfo['mem_cached'] = round($buf[3][0]/1024,2);
        $memoryInfo['mem_used'] = $memoryInfo['mem_total'] - $memoryInfo['mem_free'];
        $memoryInfo['mem_usage'] = round($memoryInfo['mem_used'] / $memoryInfo['mem_total'] * 100, 2);
        $memoryInfo['mem_percent'] = (floatval($memoryInfo['mem_total']) != 0) ? round($memoryInfo['mem_used'] / $memoryInfo['mem_total'] * 100, 2) : 0;
        $memoryInfo['mem_real_used'] = $memoryInfo['mem_total'] - $memoryInfo['mem_free'] - $memoryInfo['mem_cached'] - $memoryInfo['mem_buffers'];//Real memory use
        $memoryInfo['mem_real_free'] = $memoryInfo['mem_total'] - $memoryInfo['mem_real_used'];//True leisure
        $memoryInfo['mem_real_percent'] = (floatval($memoryInfo['mem_total']) != 0) ? round($memoryInfo['mem_real_used'] / $memoryInfo['mem_total'] * 100, 2) : 0;//Real memory usage rate
        $memoryInfo['mem_cached_percent'] = (floatval($memoryInfo['mem_cached'])!=0) ? round($memoryInfo['mem_cached'] / $memoryInfo['mem_total'] * 100, 2) : 0;//Cached memory usage rate
        $memoryInfo['swap_total'] = round($buf[4][0] / 1024,2);
        $memoryInfo['swap_free'] = round($buf[5][0] / 1024,2);
        $memoryInfo['swap_used'] = round($memoryInfo['swap_total'] - $memoryInfo['swap_free'],2);
        $memoryInfo['swap_percent'] = (floatval($memoryInfo['swap_total']) != 0) ? round($memoryInfo['swap_used'] / $memoryInfo['swap_total'] * 100, 2) : 0;

        return $memoryInfo;
    }

    /**
     * Get the current operating time of the server
     * @return string
     */
    public function getUptime() : string
    {
        $uptime  = '';
        if (false == ($str = @file("/proc/uptime"))) return '';
        $str = explode(' ',implode("", $str));
        $str = trim($str[0]);
        $min = $str / 60;
        $hours = $min / 60;
        $days = floor($hours / 24);
        $hours = floor($hours - ($days * 24));
        $min = floor($min - ($days * 60 * 24) - ($hours * 60));
        if($days !== 0)$uptime = $days . "day";
        if($hours !== 0) $uptime .= $hours . "hour";
        $uptime .= $min . "minute";

        return $uptime;
    }

    /**
     * Get the server -related information
     * @param Request $request
     * @return array
     */
    public function getServeInfo() : array
    {

        $serveInfo = [];
        $serveInfo['serve_name'] = php_uname();
        $serveInfo['php_version'] = PHP_VERSION;
        $serveInfo['zend_version'] = Zend_Version();
        $serveInfo['os'] = PHP_OS;
        $serveInfo['upload_limit'] = get_cfg_var("upload_max_filesize");
        $serveInfo['upload_limit'] = get_cfg_var("upload_max_filesize");
        $serveInfo['serve_time'] = get_cfg_var("max_execution_time") . "Second";
        $serveInfo['php_run_type'] = php_sapi_name();
        $serveInfo['architecture'] = 'Hyperf + Vue + Element';

        return $serveInfo;
    }
}