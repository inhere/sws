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
 * @Target("ALL")
 */
class Parameter
{
    /**
     * @var string
     */
    public $name;

    /**
     * @var string
     */
    public $description;

    /**
     * @var string
     */
    public $type;

    /**
     * @var mixed
     */
    public $default;

    /**
     * @var mixed
     */
    public $rule;

    /**
     * @var boolean
     */
    public $required = false;

    /**
     * Object constructor.
     * @param array $values
     */
    public function __construct(array $values)
    {
        if (isset($values['value'])) {
            $this->name = $values['value'];
        }

        foreach (['name', 'description', 'type', 'rule', 'default', 'required'] as $name) {
            if (isset($values[$name])) {
                $this->$name = $values[$name];
            }
        }
    }
}