<?php
declare(strict_types=1);

namespace App\Foundation\Annotation;

use Hyperf\Di\Annotation\AbstractAnnotation;
use Hyperf\Di\Annotation\AnnotationCollector;

/**
 * Annotation explanation of the annotation category, user record operation log and other operations
 * @Annotation
 * @Target({"METHOD"})
 */
class Explanation extends AbstractAnnotation
{
    /**
     * @var string
     */
    public $content = '';
}

