<?php
/**
 * Created by PhpStorm.
 * User: inhere
 * Date: 2017-08-29
 * Time: 11:38
 */

namespace Sws\Context;

use inhere\library\traits\PropertyAccessByGetterSetterTrait;

/**
 * Class Context
 * @package Sws\Context
 */
abstract class Context implements ContextInterface
{
    use PropertyAccessByGetterSetterTrait;

    /**
     * @var string
     */
    private $id;

    /**
     * Context constructor.
     * @param null $id
     * @param bool $addToManager
     */
    public function __construct($id = null, $addToManager = true)
    {
        $this->id = $id;

        if ($addToManager) {
            ContextManager::addContext($this);
        }
    }

    /**
     * destructor
     */
    public function __destruct()
    {
        $this->destroy();
    }

    /**
     * destroy
     */
    public function destroy()
    {
        ContextManager::delContext($this->id);
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param string $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }
}