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
     * it is `request->fd` OR `\Swoole\Coroutine::getuid()`
     * @var int|string
     */
    private $id;

    /**
     * @var string
     */
    private $key;

    /**
     * @param $id
     * @return string
     */
    public static function genKey($id)
    {
        return md5($id);
    }

    /**
     * Context constructor.
     * @param bool $addToManager
     */
    public function __construct($addToManager = true)
    {
        if ($addToManager) {
            ContextManager::addContext($this);
        }

        $this->init();
    }

    protected function init()
    {
        // somethings ...
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
     * @return int|string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int|string $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * @param string $key
     */
    public function setKey($key)
    {
        $this->key = $key;
    }
}
