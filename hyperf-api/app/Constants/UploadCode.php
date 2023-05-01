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
 * Class UploadCode
 * Upload the relevant error code
 * @Constants
 * @package App\Constants
 * @Author YiYuan-Lin
 * @Date: 2021/1/18
 * Custom business code specifications are as follows：
 * Upload related，4001……
 */
class UploadCode extends AbstractConstants
{
    /**
     * @Message("Upload file type is incorrect")
     */
    const ERR_UPLOAD_TYPE = 4001;

    /**
     * @Message("The size of the upload file is incorrect")
     */
    const ERR_UPLOAD_SIZE = 4002;

}

