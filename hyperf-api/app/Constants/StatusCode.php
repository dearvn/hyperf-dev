<?php

declare(strict_types=1);
/**
 * This file is part of Hyperf.
 *
 * @link     https://www.hyperf.io
 * @document https://doc.hyperf.io
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf-cloud/hyperf/blob/master/LICENSE
 */

namespace App\Constants;

use Hyperf\Constants\AbstractConstants;
use Hyperf\Constants\Annotation\Constants;

/**
 * Class StatusCode
 * Error code enumeration category
 * @Constants
 * @package App\Constants
 * @Author YiYuan-Lin
 * @Date: 2020/9/18
 * Custom business code specifications are as follows:
 * Authorization related, 1001 ...
 * User -related, 2001 ...
 * Business -related, 3001 ...
 */
class StatusCode extends AbstractConstants
{
    /**
     * @Message("ok")
     */
    const SUCCESS = 200;

    /**
     * @Message("Do not pass the verification!")
     */
    const ERR_NOT_ACCESS = 401;

    /**
     * @Message("No permission to visit!")
     */
    const ERR_NOT_PERMISSION = 403;


    /**
     * @Message("Internal Server Error!")
     */
    const ERR_SERVER = 500;

    /**
     * @Message("System maintenance...!")
     */
    const ERR_MAINTAIN = 404;


    /**
     * @Message("Tokens！")
     */
    const ERR_EXPIRE_TOKEN = 1002;

    /**
     * @Message("Token invalid！")
     */
    const ERR_INVALID_TOKEN = 1003;

    /**
     * @Message("The token does not exist！")
     */
    const ERR_NOT_EXIST_TOKEN = 1004;

    /**
     * @Message("Verification code error！")
     */
    const ERR_CODE = 1005;

    /**
     * @Message("Please sign in！")
     */
    const ERR_NOT_LOGIN = 2001;

    /**
     * @Message("User information error！")
     */
    const ERR_USER_INFO = 2002;

    /**
     * @Message("User does not exist！")
     */
    const ERR_USER_ABSENT = 2003;

    /**
     * @Message("User password error！")
     */
    const ERR_USER_PASSWORD= 2004;

    /**
     * @Message("User is banned！")
     */
    const ERR_USER_DISABLE= 2005;

    /**
     * @Message("Username has been used！")
     */
    const ERR_USER_EXIST= 2006;


    /**
     * @Message("Registration failed！")
     */
    const ERR_REGISTER_ERROR = 2007;


    /**
     * @Message("Business logic abnormal！")
     */
    const ERR_EXCEPTION = 3001;

    /**
     * @Message("Verifying abnormalities！")
     */
    const ERR_VALIDATION = 3002;
}
