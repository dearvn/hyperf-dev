<?php
namespace App\Foundation\Aspect;

use App\Foundation\Annotation\Explanation;
use App\Service\System\OperateLogService;
use App\Model\System\OperateLog;
use Hyperf\Di\Annotation\Aspect;
use Hyperf\Di\Annotation\Inject;
use Hyperf\Di\Aop\AbstractAspect;
use Hyperf\Di\Aop\ProceedingJoinPoint;
use Hyperf\HttpServer\Contract\RequestInterface;

/**
 * Operation log cut surface (for recording operating logs)
 * @Aspect
 */
class OperateLogAspect extends AbstractAspect
{
    /**
     * @Inject()
     * @var RequestInterface
     */
    protected $request;

    // To cut in, you can have multiple, or you can use :: identify a specific method. Through *, you can blur the matching
    public $classes = [
    ];

    // To cut into the annotation, the specific cut is used to use these annotations, only the category annotation and class method annotation can be used.
    public $annotations = [
        Explanation::class
    ];

    /**
     * Record the cutting of the operation log
     * @param ProceedingJoinPoint $proceedingJoinPoint
     * @return mixed
     * @throws \Hyperf\Di\Exception\Exception
     */
    public function process(ProceedingJoinPoint $proceedingJoinPoint)
    {
        // Do certain processing before calling
        $logData = OperateLogService::getInstance()->collectLogInfo();

        $result = $proceedingJoinPoint->process();

        //Request processing
        $responseResult = json_decode($result->__toString(), true);
        $logData['response_result'] = $responseResult['msg'];
        $logData['response_code'] = $responseResult['code'];
        if (!empty($logData['action'])) OperateLog::add($logData);

        return $result;
    }
}