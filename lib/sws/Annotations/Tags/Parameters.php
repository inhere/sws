<?php
/**
 * Created by PhpStorm.
 * User: inhere
 * Date: 2017-09-26
 * Time: 12:01
 */

namespace Sws\Annotations\Tags;

use Doctrine\Common\Annotations\Annotation\Target;

/**
 * Class Parameters
 * @package Sws\Annotations\Tags
 *
 * @Annotation
 * @Target("METHOD")
 */
class Parameters
{
    /**
     * @var array<Sws\Annotations\Tags\Parameter>
     */
    public $value;
}