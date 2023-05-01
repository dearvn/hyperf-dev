<?php
namespace App\Service\System;

use App\Foundation\Annotation\Explanation;
use App\Foundation\Traits\Singleton;
use App\Service\BaseService;
use Hyperf\Di\Annotation\AnnotationCollector;
use Hyperf\HttpServer\Router\Dispatched;

/**
 * Operation log service
 * Class OperateLogService
 * @package App\Service\System
 * @Author YiYuan-Lin
 * @Date: 2020/12/16
 */
class OperateLogService extends BaseService
{
    use Singleton;

    /**
     * Collect the operation log information
     * @return array
     */
    public function collectLogInfo() : array
    {
        //Get the request parameter
        $requireParams = $this->request->all();
        //Get the target controller and method
        $requestController = $this->request->getAttribute(Dispatched::class)->handler->callback;
        $actionController = $requestController[0];
        $actionMethod = $requestController[1];
        //Get the request route
        $actionUrl = $this->request->getUri()->getPath();
        //Get the annotation information
        $explanation = AnnotationCollector::getMethodsByAnnotation(Explanation::class);
        $classMethodsExplanation = [];
        foreach ($explanation as $key => $value) {
            $classMethodsExplanation[$value['class']][$value['method']] = $value['annotation']->content;
        }
        $content = $classMethodsExplanation[$actionController][$actionMethod] ?? '';
        if (empty($content))  return [];

        //Get user information
        $userInfo = conGet('user_info');
        if (empty($userInfo)) return [];

        return [
            'action' => $content ?? '',
            'data' => json_encode($requireParams) ?? [],
            'username' => $userInfo['username'] ?? '',
            'operator' => $userInfo['desc'] ?? '',
            'uid' => $userInfo['id'] ?? '',
            'target_class' => $actionController ?? '',
            'target_method' => $actionMethod ?? '',
            'target_url' => $actionUrl ?? '',
            'request_ip' => getClientIp($this->request) ?? '',
            'request_method' => ucwords($this->request->getMethod()) ?? '',
        ];
    }


}
