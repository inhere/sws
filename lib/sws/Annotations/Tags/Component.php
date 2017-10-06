<?php
/**
 * Created by PhpStorm.
 * User: inhere
 * Date: 2017-09-11
 * Time: 16:36
 */

namespace Sws\Annotations\Tags;

use Doctrine\Common\Annotations\Annotation\Required;
use Doctrine\Common\Annotations\Annotation\Target;

/**
 * Class Component - mark class is an Component of the DI container
 * @package Sws\Annotations\Tags
 * @deprecated please use class Service instead it.
 *
 * @Annotation
 * @Target("CLASS")
 */
final class Component
{
    /**
     * @Required()
     * @var string
     */
    public $name;

    /**
     * @var bool
     */
    public $shared = true;

    /**
     * @var array
     */
    public $alias;

    /**
     * @var bool
     */
    public $activate = true;

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

        if (isset($values['alias'])) {
            $this->alias = (array)$values['alias'];
        }

        if (isset($values['shared'])) {
            $this->shared = (bool)$values['shared'];
        }

        if (isset($values['activate'])) {
            $this->activate = (bool)$values['activate'];
        }
    }
}
