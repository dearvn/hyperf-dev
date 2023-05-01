<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Constants\StatusCode;
use App\Exception\Handler\BusinessException;
use App\Model\System\GlobalConfig;
use App\Service\Auth\UserService;
use Hyperf\Di\Annotation\Inject;
use Hyperf\Utils\Context;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * Whether the monitoring system is in maintenance
 * Class CheckMaintainMiddleware
 * @package App\Middleware
 * @Author YiYuan-Lin
 * @Date: 2021/06/17
 */
class CheckMaintainMiddleware implements MiddlewareInterface
{
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $writeRoute = ['/common/sys_config', '/common/auth/verification_code', '/auth/login', '/auth/register', '/test', ];
        if (in_array($request->getUri()->getPath(), $writeRoute)) return $handler->handle($request);

        //Get the current user
        $user = UserService::getInstance()->getUserInfoByToken();
        //Determine whether it is a super administrator
        if ($user->hasRole('super_admin')) return $handler->handle($request);

        // Get the background maintenance status
        $maintain = GlobalConfig::getOneByKeyName('maintain_switch');
        $isMaintain = (bool)$maintain['data'];
        // Determine whether the background is in maintenance state
        if ($isMaintain) Throw new BusinessException(StatusCode::ERR_MAINTAIN, 'During the system maintenance, please contact the administrator if necessary');

        // Get the simple maintenance status of the background
        $simpleMaintain = GlobalConfig::getOneByKeyName('simple_maintain_switch');
        $isSimpleMaintain = (bool)$simpleMaintain['data'];
        $httpMethod = $request->getMethod();

        // Determine whether the background is in a simple maintenance state
        if ($isSimpleMaintain && $httpMethod != 'GET') {
            Throw new BusinessException(StatusCode::ERR_MAINTAIN, 'In the system maintenance, you can only perform the query operation. If necessary, please contact the administrator');
        }
        return $handler->handle($request);
    }

}