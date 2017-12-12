<?php
/**
 * Created by PhpStorm.
 * User: inhere
 * Date: 2017-08-31
 * Time: 15:16
 */

namespace Sws\Web;

use Swoole\Http\Request as SwRequest;
use Swoole\Http\Response as SwResponse;

use Sws\Context\AbstractContext;
use Sws\Coroutine\Coroutine;

/**
 * Class HttpContext
 * @package Sws\Web
 */
class HttpContext extends AbstractContext
{
    /** @var array  */
    private static $dataTypes = [
        'html' => 'text/html',
        'txt' => 'text/plain',
        'js' => 'application/javascript',
        'css' => 'text/css',
        'json' => 'application/json',
        'xml' => 'text/xml',
        'rdf' => 'application/rdf+xml',
        'atom' => 'application/atom+xml',
        'rss' => 'application/rss+xml',
        'form' => 'application/x-www-form-urlencoded'
    ];

    /**
     * @param SwRequest $swRequest
     * @param SwResponse $swResponse
     * @return static
     */
    public static function make(SwRequest $swRequest, SwResponse $swResponse)
    {
        return new static($swRequest, $swResponse);
    }

    /**
     * object constructor.
     * @param SwRequest $swRequest
     * @param SwResponse $swResponse
     */
    public function __construct(SwRequest $swRequest, SwResponse $swResponse)
    {
        $this->setRequestResponse($swRequest, $swResponse);

        parent::__construct();

        \Sws::getCtxManager()->add($this);
    }

    /**
     * @return array
     */
    public static function getDataTypes(): array
    {
        return self::$dataTypes;
    }

    protected function init()
    {
        $id = Coroutine::id();

        $this->setId($id);
        $this->setKey(static::genKey($id));
    }

    /**
     * destroy
     */
    public function destroy()
    {
        \Sws::getCtxManager()->del($this->getId());

        parent::destroy();
    }

}
