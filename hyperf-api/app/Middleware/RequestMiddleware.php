<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Constants\StatusCode;
use App\Exception\Handler\BusinessException;
use App\Foundation\Facades\Log;
use App\Service\Auth\UserService;
use Hyperf\HttpServer\Contract\RequestInterface;
use Hyperf\Utils\Context;
use Phper666\JWTAuth\Exception\TokenValidException;
use Phper666\JWTAuth\Exception\JWTException;
use Phper666\JWTAuth\JWT;
use Phper666\JWTAuth\Util\JWTUtil;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class RequestMiddleware implements MiddlewareInterface
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
     * @var JWT
     */
    protected $jwt;

    public function __construct(ContainerInterface $container, RequestInterface $request, JWT $jwt)
    {
        $this->container = $container;
        $this->request = $request;
        $this->jwt = $jwt;
    }

    /**
     * Request token token middleware
     * @param ServerRequestInterface $request
     * @param RequestHandlerInterface $handler
     * @return ResponseInterface
     * @throws \Psr\SimpleCache\InvalidArgumentException
     * @throws \Throwable
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $requireParams = $this->request->all();
        //Record request parameter log record
        if (config('request_log')) Log::requestLog()->info('Request parameters：' . json_encode($requireParams));
        try {
            $isValidToken = false;
            // According to the specific business judgment logic, it is assuming that the token of the user carried is valid
            $token = $request->getHeaderLine('Authorization') ?? '';
            if (strlen($token) > 0) {
                $token = JWTUtil::handleToken($token);
                if ($token !== false && $this->jwt->checkToken($token)) $isValidToken = true;

                //If the verification is successful
                if ($isValidToken) {
                    //In the context of the user information placement correction
                    $userInfo = UserService::getInstance()->getUserInfoByToken();
                    conSet('user_info', $userInfo);
                    return $handler->handle($request);
                }
            }
        }catch (TokenValidException $e) {
            //The TokenValidException thrown by token expiration is caught here
            //What we need to do here is refresh the user's token and add it to the response header
            try {
                //Refresh the user's token
                $token = $request->getHeaderLine('Authorization') ?? '';
                $token = JWTUtil::handleToken($token);
                $tokenData = $this->jwt->getParserData($token);

                //Determine whether the token is within the cache time, if it is refreshing token
                if (time()-$tokenData['exp'] < intval(config('jwt.ttl_cache'))) {
                    $token = $this->jwt->refreshToken();
                    //Obtain the global response object from the coroutine, and write the token to write the head and return to the front end for the front end to refresh token
                    $response = conGet(ResponseInterface::class);
                    //Add the refreshing token to the return object and the Access-Control-EXPOSE-Headers (let the front end get our custom Authorization))
                    $response = $response->withHeader('authorization', $token)->withHeader('Access-Control-Expose-Headers', 'authorization');
                    conSet(ResponseInterface::class, $response);

                    //In the context of the user information placement correction
                    $userInfo = UserService::getInstance()->getUserInfoByToken();
                    conSet('user_info', $userInfo);

                    return $handler->handle($request);
                }

                throw new TokenValidException('Token has expired', 401);
            } catch (JWTException $exception) {
                // If this is abnormal, it means that the Refresh has expired. The user cannot refresh the token and need to log in again.
                throw new TokenValidException($exception->getMessage(), $exception->getCode());
            }
        }

        Throw new BusinessException(StatusCode::ERR_INVALID_TOKEN, 'Token is invalid or expired');
    }
}
