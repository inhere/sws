<?php
/**
 * Created by PhpStorm.
 * User: inhere
 * Date: 2017-09-11
 * Time: 17:10
 */

namespace Sws\Annotations;

use Doctrine\Common\Annotations\Annotation\Target;

/**
 * Class Controller
 * @package Sws\Annotations
 *
 * @Annotation
 * @Target("CLASS")
 */
final class Controller
{
    // normal controller
    const NORM = 1;
    // restFul controller
    const REST = 2;

    /**
     * @var string
     */
    public $type = self::NORM;

    /**
     * the route prefix path
     * @var string
     */
    public $prefix = '';

    /**
     * on enter
     * @var mixed
     */
    public $enter;

    /**
     * on leave
     * @var mixed
     */
    public $leave;

    /**
     * Route constructor.
     * @param array $values
     */
    public function __construct(array $values)
    {
        if (isset($values['value'])) {
            $this->prefix = $values['value'];
        }

        foreach (['prefix', 'type', 'enter', 'leave'] as $name) {
            if (isset($values[$name])) {
                $this->$name = $values[$name];
            }
        }
    }
}
