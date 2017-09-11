<?php
/**
 * Created by PhpStorm.
 * User: inhere
 * Date: 2017-08-31
 * Time: 9:43
 */

namespace Sws\Annotations;

use Doctrine\Common\Annotations\Annotation\Required;
use Doctrine\Common\Annotations\Annotation\Target;

/**
 * Class Route RouteDocBlock
 * @package Sws\Annotations
 *
 * @Annotation
 * @Target("METHOD")
 */
final class Route
{
    /**
     * the route path
     * @var string
     * @Required()
     */
    public $path = '';

    /**
     * -Enum({"GET", "POST", "PUT", "PATCH", "DELETE", "HEAD", "OPTIONS"})
     * @var string
     */
    public $method = 'GET';

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
            $this->path = $values['value'];
        }

        foreach (['path', 'method', 'enter', 'leave'] as $name) {
            if (isset($values[$name])) {
                $this->$name = $values[$name];
            }
        }
    }
}
