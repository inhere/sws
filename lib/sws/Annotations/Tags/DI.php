<?php
/**
 * Created by PhpStorm.
 * User: inhere
 * Date: 2017-09-11
 * Time: 13:53
 */

namespace Sws\Annotations\Tags;

use Doctrine\Common\Annotations\Annotation\Target;

/**
 * Class DI - Dependency Injection
 * @package Sws\Annotations\Tags
 *
 * @Annotation
 * @Target({"PROPERTY", "METHOD"})
 */
final class DI
{
    /**
     * @var string
     */
    public $name = '';

    /**
     * Object constructor.
     * @param array $values
     */
    public function __construct(array $values)
    {
        if (isset($values['value'])) {
            $this->name = $values['value'];
        } elseif (isset($values['name'])) {
            $this->name = $values['name'];
        }
    }
}
