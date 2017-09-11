<?php
/**
 * Created by PhpStorm.
 * User: inhere
 * Date: 2017-08-31
 * Time: 9:54
 */

namespace Sws\Annotations;

use Doctrine\Common\Annotations\Annotation\Required;
use Doctrine\Common\Annotations\Annotation\Target;

/**
 * Class AopPoint
 * @package Sws\Annotations
 *
 * @Annotation
 * @Target({"CLASS", "METHOD"})
 */
final class AopPoint
{
    /**
     * @var string
     * @Required()
     */
    public $name;

    /**
     * @var string
     */
    public $handler;

    /**
     * Object constructor.
     * @param array $values
     */
    public function __construct(array $values)
    {
        if (isset($values['value'])) {
            $this->name = $values['value'];
        }

        foreach (['name', 'handler'] as $name) {
            if (isset($values[$name])) {
                $this->$name = $values[$name];
            }
        }
    }
}
