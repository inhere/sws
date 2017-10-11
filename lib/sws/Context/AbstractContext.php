<?php
/**
 * Created by PhpStorm.
 * User: inhere
 * Date: 2017-08-29
 * Time: 11:38
 */

namespace Sws\Context;

use Inhere\Library\Traits\PropertyAccessByGetterSetterTrait;
use Inhere\Library\Traits\ArrayAccessByPropertyTrait;

use Inhere\Http\Request;
use Inhere\Http\Response;

use Swoole\Http\Request as SwRequest;
use Swoole\Http\Response as SwResponse;
use Sws\Components\HttpHelper;

/**
 * Class Context
 * @package Sws\Context
 */
abstract class AbstractContext implements ContextInterface, \ArrayAccess
{
    use ArrayAccessByPropertyTrait;
    use PropertyAccessByGetterSetterTrait;

    /**
     * it is `request->fd` OR `\Swoole\Coroutine::getuid()`
     * @var int|string
     */
    protected $id = 0;

    /**
     * a unique ID string generate by $id
     * @var string
     */
    protected $key;

    /**
     * @var Request
     */
    protected $request;

    /**
     * @var Response
     */
    protected $response;

    /**
     * @var SwRequest
     */
    protected $swRequest;

    /**
     * @var SwResponse
     */
    protected $swResponse;

    /**
     * @param $id
     * @return string
     */
    public static function genKey($id)
    {
        return md5($id . getmypid());
    }

    /**
     * Context constructor.
     */
    public function __construct()
    {
        $this->init();
    }

    protected function init()
    {
        // somethings ...
    }

    /**
     * @param SwRequest $swRequest
     * @param SwResponse $swResponse
     */
    public function setRequestResponse(SwRequest $swRequest, SwResponse $swResponse)
    {
        $this->request = HttpHelper::createRequest($swRequest);
        $this->response = HttpHelper::createResponse();

        $this->swRequest = $swRequest;
        $this->swResponse = $swResponse;
    }


    /**
     * @return array
     */
    public function all()
    {
        return [
            'id' => $this->id,
            'key' => $this->key,
        ];
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
        $this->id = $this->key = null;
        $this->request = $this->response = $this->swRequest = $this->swResponse = null;
    }

    /*******************************************************************************
     * getter/setter methods
     ******************************************************************************/

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

    /**
     * @return Request
     */
    public function getRequest(): Request
    {
        return $this->request;
    }

    /**
     * @param Request $request
     */
    public function setRequest(Request $request)
    {
        $this->request = $request;
    }

    /**
     * @return Response
     */
    public function getResponse(): Response
    {
        return $this->response;
    }

    /**
     * @param Response $response
     */
    public function setResponse(Response $response)
    {
        $this->response = $response;
    }

    /**
     * @return SwRequest
     */
    public function getSwRequest(): SwRequest
    {
        return $this->swRequest;
    }

    /**
     * @param SwRequest $swRequest
     */
    public function setSwRequest(SwRequest $swRequest)
    {
        $this->swRequest = $swRequest;
    }

    /**
     * @return SwResponse
     */
    public function getSwResponse(): SwResponse
    {
        return $this->swResponse;
    }

    /**
     * @param SwResponse $swResponse
     */
    public function setSwResponse(SwResponse $swResponse)
    {
        $this->swResponse = $swResponse;
    }

}
