<?php
/**
 * Created by PhpStorm.
 * User: inhere
 * Date: 2017-08-31
 * Time: 10:04
 */

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
     */
    public function indexAction()
    {
        // something ... ...
    }
}