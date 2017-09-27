<?php
/**
 * Created by PhpStorm.
 * User: inhere
 * Date: 2017-09-11
 * Time: 17:10
 */

namespace Sws\Annotations\Tags;

use Doctrine\Common\Annotations\Annotation\Target;

/**
 * Class Model - mark class is an model class
 * @package Sws\Annotations\Tags
 *
 * @Annotation
 * @Target("CLASS")
 */
final class Model
{
    /**
     * @var string
     */
    public $name;

    /**
     * @var string
     */
    public $table;

    /**
     * Object constructor.
     * @param array $values
     */
    public function __construct(array $values)
    {
        if (isset($values['value'])) {
            $this->name = $values['value'];
        }

        foreach (['name', 'table'] as $name) {
            if (isset($values[$name])) {
                $this->$name = $values[$name];
            }
        }
    }
}
