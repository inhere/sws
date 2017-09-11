<?php
/**
 * Created by PhpStorm.
 * User: inhere
 * Date: 2017/9/11
 * Time: 下午11:15
 */

namespace Sws\Annotations;

use Doctrine\Common\Annotations\Annotation\Required;
use Doctrine\Common\Annotations\Annotation\Target;

/**
 * Class WsModule
 * @package Sws\Annotations
 *
 * @Annotation
 * @Target("CLASS")
 */
final class WsModule
{
    /**
     * @var string
     */
    public $name;

    /**
     * @var string
     * @Required()
     */
    public $path;

    /**
     * Object constructor.
     * @param array $values
     */
    public function __construct(array $values)
    {
        if (isset($values['value'])) {
            $this->name = $values['value'];
        }

        foreach (['name', 'path'] as $name) {
            if (isset($values[$name])) {
                $this->$name = $values[$name];
            }
        }
    }
}
