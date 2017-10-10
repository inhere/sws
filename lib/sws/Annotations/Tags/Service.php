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
 * Class Service - mark class is an Service of the DI container
 * @package Sws\Annotations\Tags
 *
 * @Annotation
 * @Target("CLASS")
 */
final class Service
{
    const POOL = 'pool';
    const SHARED = 'shared';
    const SINGLETON = 'singleton';

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
     * @var bool
     */
    public $scope = self::SHARED;

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
