<?php
/**
 * Created by PhpStorm.
 * User: inhere
 * Date: 2017-08-31
 * Time: 10:04
 */
use Sws\annotations\Route;

/**
 * Class DocBlockExample
 *
 * @prefix('/test') - the route prefix path
 * @beforeExecute('myHandler')
 */
class DocBlockExample
{
    /**
     * @beforeExecute('myIndexActionHandler')
     *
     * @method('GET')
     * @route('index')
     *
     * @Route("index", method="GET")
     */
    public function indexAction()
    {
        // something ... ...
    }
}