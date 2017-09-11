<?php
/**
 * Created by PhpStorm.
 * User: inhere
 * Date: 2017-09-11
 * Time: 11:09
 */

namespace Sws\Annotations;

use Doctrine\Common\Annotations\Annotation\Target;

/**
 * Class RoutePrefix
 * @package Sws\Annotations
 *
 * @Annotation
 * @Target("CLASS")
 */
class RoutePrefix
{
    /**
     * @var string
     */
    public $value = '';
}