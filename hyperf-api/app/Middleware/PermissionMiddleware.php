<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Constants\StatusCode;
use App\Exception\Handler\BusinessException;
use App\Service\Auth\UserService;
use App\Model\Auth\Role;
use Hyperf\HttpServer\Contract\RequestInterface;
use Hyperf\HttpServer\Contract\ResponseInterface as HttpResponse;
use Hyperf\HttpServer\Router\Dispatched;
use Hyperf\Utils\Context;
use Phper666\JWTAuth\JWT;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class PermissionMiddleware implements MiddlewareInterface
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @var RequestInterface
     */
    protected $request;

    /**
     * @var HttpResponse
     */
    protected $response;

    /**
     * @var JWT
     */
    protected $jwt;

    public function __construct(ContainerInterface $container, HttpResponse $response, RequestInterface $request, JWT $jwt)
    {
        $this->container = $container;
        $this->response = $response;
        $this->request = $request;
        $this->jwt = $jwt;
    }

    /**
     * Permanent verification intermediate parts
     * @param ServerRequestInterface $request
     * @param RequestHandlerInterface $handler
     * @return ResponseInterface
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        //Get access target controller and method
        $requestController = $this->request->getAttribute(Dispatched::class)->handler->callback;
        $controller = $requestController[0];
        $actionMethod = $requestController[1];
        $actionName = 'Api:' .  ltrim($request->getUri()->getPath(), '/'). '-' . $actionMethod;
        $actionName = preg_replace('/\/\d+/', '', $actionName);

        //Get the current user
        $user = UserService::getInstance()->getUserInfoByToken();
        Context::set('user_info', $user);

        //Determine whether it is a super administrator
        if ($user->hasRole(Role::SUPER_ADMIN)) return $handler->handle($request);
        if (!$user->can($actionName)) Throw new BusinessException(StatusCode::ERR_NOT_PERMISSION, 'No Permanent Visit');

        return $handler->handle($request);
    }
}
