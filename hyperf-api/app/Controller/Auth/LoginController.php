<?php

declare(strict_types=1);

namespace App\Controller\Auth;

use App\Constants\StatusCode;
use App\Controller\AbstractController;
use App\Service\Auth\LoginService;
use App\Middleware\RequestMiddleware;
use Hyperf\Di\Annotation\Inject;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\Middleware;
use Hyperf\HttpServer\Annotation\Middlewares;
use Hyperf\HttpServer\Annotation\RequestMapping;
use Phper666\JWTAuth\JWT;

/**
 * Login controller
 * @Controller(prefix="auth")
 */
class LoginController extends AbstractController
{
    /**
     * @Inject()
     * @var JWT
     */
    private $jwt;

    /**
     * Login operation
     * @RequestMapping(path="login", methods="post")
     * @return \Psr\Http\Message\ResponseInterface
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function login()
    {
        $params = [
            'username' =>  $this->request->input('username') ?? '',
            'password' => $this->request->input('password') ?? '',
            'code_key' => $this->request->input('code_key') ?? '',
            'captcha' => $this->request->input('captcha') ?? '',
        ];
        $rules = [
            'username' => 'required',
            'password' => 'required|min:6|max:18',
            'code_key' => 'required',
            'captcha' => 'required',
        ];
        $message = [
            'username.required' => ' username required',
            'password.required' => ' password required',
            'password.min' => ' password at least 6 digits',
            'password.max' => ' password up to 18 digits',
            'code_key.required' => 'Verification code Key is missing',
            'captcha.required' => 'Verification code required',
        ];
        $this->verifyParams($params, $rules, $message);

        $responseData = LoginService::getInstance()->login($params);
        return $this->success($responseData);
    }

    /**
     * Register
     * @RequestMapping(path="register", methods="post")
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function register()
    {
        $params = [
            'username' =>  $this->request->input('username') ?? '',
            'password' => $this->request->input('password') ?? '',
            'password_confirmation' => $this->request->input('password_confirmation') ?? '',
            'desc' => $this->request->input('desc') ?? '',
            'code_key' => $this->request->input('code_key') ?? '',
            'captcha' => $this->request->input('captcha') ?? '',
        ];
        $rules = [
            'username' => 'required|min:4|max:18|unique:users',
            'password' => 'required|min:6|max:18|confirmed:password_confirmation',
            'password_confirmation' => 'required|min:6|max:18',
            'desc' => 'required',
            'code_key' => 'required',
            'captcha' => 'required',
        ];
        $message = [
            'username.required' => ' username required',
            'username.unique' => 'This user name already exists',
            'username.min' => '[username] at least 4 digits',
            'username.max' => '[username] up to 18 digits',
            'desc.required' => ' desc required',
            'password.required' => ' password required',
            'password_confirmation.required' => ' password required',
            'password.min' => ' password at least 6 digits',
            'password_confirmation.min' => ' password at least 6 digits',
            'password.max' => ' password up to 18 digits',
            'password_confirmation.max' => ' password Up to 18 digits',
            'code_key.required' => 'Verification code Key is missing',
            'captcha.required' => 'Verification code required',
        ];
        $this->verifyParams($params, $rules, $message);

        $result = LoginService::getInstance()->register($params);
        if (!$result) $this->throwExp(StatusCode::ERR_REGISTER_ERROR, 'registration failed');

        return $this->successByMessage('Successful registration, jumping and landing...');
    }


    /**
     * Initialization operation
     * @RequestMapping(path="initialization", methods="get")
     * @Middlewares({
            @Middleware(RequestMiddleware::class)
 *     })
     * @return \Psr\Http\Message\ResponseInterface
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function initialization()
    {
        $list = LoginService::getInstance()->initialization();
        return $this->success($list);
    }

    /**
     * Get the front-end route
     * @RequestMapping(path="routers", methods="get")
     * @Middlewares({
            @Middleware(RequestMiddleware::class)
     * })
     * @return \Psr\Http\Message\ResponseInterface
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function getRouters()
    {
        $list = LoginService::getInstance()->getRouters();
        return $this->success($list);
    }

    /**
     * Exit login operation
     * @RequestMapping(path="logout", methods="post")
     * @Middlewares({
            @Middleware(RequestMiddleware::class)
*     })
     * @return \Psr\Http\Message\ResponseInterface
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function logOut()
    {
        $this->jwt->logout();
        return $this->success([], 'Successfully withdraw from the login');
    }
}
