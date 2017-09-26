<?php
/**
 * Created by PhpStorm.
 * User: inhere
 * Date: 2017-09-26
 * Time: 12:01
 */

namespace Sws\Annotations;

use Doctrine\Common\Annotations\Annotation\Target;

/**
 * Class Parameters
 * @package Sws\Annotations
 *
 * @Annotation
 * @Target("METHOD")
 */
class Parameters
{
    /**
     * @var array<Sws\Annotations\Parameter>
     */
    public $value;
}