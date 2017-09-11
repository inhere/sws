<?php
/**
 * Created by PhpStorm.
 * User: inhere
 * Date: 2017-09-11
 * Time: 16:36
 */

namespace Sws\Annotations;

use Doctrine\Common\Annotations\Annotation\Target;

/**
 * Class Component - mark class is an Component of the DI container
 * @package Sws\Annotations
 *
 * @Annotation
 * @Target("CLASS")
 */
class Component
{
    /**
     * @var string
     */
    public $name = '';

    /**
     * @var bool
     */
    public $shared = true;

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

        if (isset($values['shared'])) {
            $this->shared = (bool)$values['shared'];
        }
    }
}