<?php
namespace App\Foundation\Traits;

use App\Constants\StatusCode;
use App\Exception\Handler\BusinessException;
use App\Foundation\Facades\Log;
use App\Service\System\LoginLogService;
use App\Service\System\OperateLogService;
use App\Model\System\LoginLog;
use App\Model\System\OperateLog;
use Hyperf\HttpServer\Contract\RequestInterface;
use Hyperf\HttpServer\Contract\ResponseInterface;
use Psr\Container\ContainerInterface;
use Hyperf\Di\Annotation\Inject;

trait ApiTrait
{
    /**
     * @Inject
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @Inject
     * @var RequestInterface
     */
    protected $request;

    /**
     * @Inject
     * @var ResponseInterface
     */
    protected $response;

    /**
     * Response
     * @param array $data
     * @param string $message
     * @return \Psr\Http\Message\ResponseInterface
     */
    protected function success($data = [], $message = '')
    {
        return $this->response->json($this->formatResponse($data, $message));
    }

    /**
     * Successful response message
     * @param string $message
     * @return \Psr\Http\Message\ResponseInterface
     */
    protected function successByMessage(string $message = '')
    {
        return $this->response->json($this->formatResponse([], $message));
    }


    /**
     * 错误响应
     * @param int $statusCode
     * @param string|null $message
     * @param bool $isRecordLog
     * @return \Psr\Http\Message\ResponseInterface
     */
    protected function error(int $statusCode = StatusCode::ERR_EXCEPTION, string $message = null, bool $isRecordLog = true)
    {
        $message = $message ?? StatusCode::ERR_EXCEPTION;

        $targetUrl = $this->request->getUri()->getPath();

        if ($targetUrl == '/auth/login') {
            //Record login abnormal log
            $loginLogData = LoginLogService::getInstance()->collectLoginLogInfo();
            $loginLogData['response_code'] = $statusCode;
            $loginLogData['response_result'] = $message;
            LoginLog::add($loginLogData);
        }else if ($isRecordLog) {
            //Record operation anomalous log
            $logData = OperateLogService::getInstance()->collectLogInfo();
            if(!empty($logData)) {
                $logData['response_result'] = $message;
                $logData['response_code'] = $statusCode;
                if (!empty($logData['action'])) OperateLog::add($logData);
            }
        }
        return $this->response->json($this->formatResponse([], $message, $statusCode));
    }

    /**
     * Throwing out business errors abnormally
     * @param int $code
     * @param string $message
     */
    public function throwExp(int $code =  StatusCode::ERR_EXCEPTION, string $message = '')
    {
        Throw new BusinessException($code, $message);
    }

    /**
     * Supervision errors abnormal response error information
     * @param Throwable $throwable
     * @return \Psr\Http\Message\ResponseInterface
     */
    protected function errorExp(Throwable $throwable)
    {
        if (!$throwable->getCode()) {
            $code = StatusCode::ERR_SERVER;
            $message = 'Server Error ' . $throwable->getMessage() . ':: FILE:' . $throwable->getFile() . ':: LINE: ' . $throwable->getLine();
        } else {
            $code = $throwable->getCode();
            $message = $throwable->getMessage();
        }
        return $this->error($code, $message);
    }

    /**
     * Format API response data
     * @param array  $data  Return data
     * @param int    $statusCode  error code
     * @param string $message Error message
     * @return array
     */
    protected function formatResponse(array $data = [], string $message = 'Success', int $statusCode = StatusCode::SUCCESS) : array
    {
        $return['code'] = $statusCode;
        $return['msg'] = $message;
        $return['data'] = $data;

        //Record request parameter log record
        if (config('response_log')) Log::responseLog()->info('Return parameter：' . json_encode($return));
        return $return;
    }
}
