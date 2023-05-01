<?php

declare(strict_types=1);

namespace App\Controller;

use App\Foundation\Facades\Log;
use App\Foundation\Utils\Mail;
use App\Job\EmailNotificationJob;
use App\Model\Laboratory\Bilibili\UpUser;
use App\Model\Laboratory\Bilibili\UpUserReport;
use App\Service\Laboratory\Bilibili\VideoService;
use Hyperf\DbConnection\Db;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\RequestMapping;

/**
 * Test the controller, which is generally used to test some code
 * Class IndexController
 * @Controller
 */
class IndexController extends AbstractController
{
    public function __construct()
    {

    }

    /**
     * Get user data list
     * @RequestMapping(path="/test", methods="get,post")
     */
    public function index()
   {
       //Distribution queue
       $this->queue->push(new EmailNotificationJob([
           'title' => $params['title'],
           'content' => $params['content'],
       ]));

//        $url = 'https://m.bilibili.com/video/BV1Q624y1q7sj';


//        $lastString = basename($url);
//        $mid = explode('?', $lastString)[0] ?? '';
//
//        $upUser = new UpUser();
//        $upUser->mid = $mid;
//        $upUser->timed_status = 1;
//        $upUser->save();
//        return $upUser->mid;
//        preg_match('/.*?av(\d{0,})\/?/', $url,$m);
//        if (!isset($m[1])) {
//            return false;
//        }
//        $vid = $m[1];//Get video UID
//
//        return $vid;

//        $infoUrl = 'https://api.bilibili.com/x/web-interface/archive/stat?aid='.$vid;//Stitching the interface address
//        $infoRespose =  doCurlGetRequest($infoUrl);
//
//        $data =  $this->bilibili($infoRespose);//Get video capture data
//        $respose = file_get_contents("https://api.bilibili.com/x/web-interface/view?aid=".$vid);
//
//        if (empty($respose) || !$respose) return false;
//        $resposeData = json_decode($respose,true)['data'];
//
//        $data['name'] = $resposeData['title'];
//        $data['time'] = date('Y-m-d H:i:s',$resposeData['ctime']);
//        $data['author'] =  $resposeData['owner']['name'];
//
//        return $data;

    }

}
