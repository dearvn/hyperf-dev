<?php
namespace App\Foundation\Traits;

use App\Constants\StatusCode;
use App\Exception\Handler\BusinessException;
use Hyperf\HttpServer\Contract\RequestInterface;
use Hyperf\HttpServer\Contract\ResponseInterface;
use Psr\Container\ContainerInterface;
use Hyperf\Di\Annotation\Inject;
use Hyperf\Validation\Contract\ValidatorFactoryInterface;

/**
 * Verifyer base class
 * Trait ValidationTrait
 * @package App\Foundation\Traits
 */
trait ValidationTrait
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
     * @Inject()
     * @var ValidatorFactoryInterface
     */
    protected $validationFactory;

    /**
     * Verifying abnormalities
     * @param $data
     * @param $rules
     * @param $message
     */
    public function verifyParams($data, $rules, $message)
    {
        $validator = $this->validationFactory->make($data, $rules, $message);
        if ($validator->fails()) {
            Throw new BusinessException(StatusCode::ERR_VALIDATION, $validator->errors()->first());
        }
    }
}
