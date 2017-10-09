<?php
/**
 * Created by PhpStorm.
 * User: inhere
 * Date: 2017-08-31
 * Time: 9:43
 */

namespace Sws\Annotations\Tags;

use Doctrine\Common\Annotations\Annotation\Required;
use Doctrine\Common\Annotations\Annotation\Target;

/**
 * Class Route RouteDocBlock
 * @package Sws\Annotations\Tags
 *
 * @Annotation
 * @Target("METHOD")
 */
final class Route
{
    /**
     * the route path
     * @var string|array
     * @Required()
     */
    public $path;

    /**
     * -Enum({"GET", "POST", "PUT", "PATCH", "DELETE", "HEAD", "OPTIONS"})
     * @var string|array
     */
    public $method = 'GET';

    /**
     * @var array
     */
    public $schemes = [];

    /**
     * @var array
     */
    public $domains = [];

    /**
     * @var array
     */
    public $tokens = [];

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

        $props = ['path', 'method', 'enter', 'leave', 'tokens', 'schemes', 'domains'];

        foreach ($props as $name) {
            if (isset($values[$name])) {
                $this->$name = $values[$name];
            }
        }
    }
}
