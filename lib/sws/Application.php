<?php
/**
 * Created by PhpStorm.
 * User: inhere
 * Date: 2017-08-25
 * Time: 9:40
 */

namespace Sws;

use Inhere\Console\Utils\Show;
use Inhere\Http\Request;
use Inhere\Http\Response;
use Inhere\Library\Helpers\Obj;
use Inhere\Library\Helpers\PhpHelper;
use Inhere\Library\Traits\EventTrait;
use Inhere\Library\Traits\OptionsTrait;
use Monolog\Logger;
use Swoole\Http\Request as SwRequest;
use Swoole\Http\Response as SwResponse;
use Swoole\Websocket\Frame;
use Sws;
use Sws\Web\HttpContextGetTrait;
use Sws\Web\HttpContext;
use Sws\WebSocket\Module\ModuleInterface;
use Sws\WebSocket\Connection;

/**
 * Class Application
 * @package Sws
 */
class Application implements ApplicationInterface
{
    use ApplicationTrait;
    use EventTrait;
    use OptionsTrait;
    use HttpContextGetTrait;

    const DATA_JSON = 'json';
    const DATA_TEXT = 'text';

    const ON_NO_MODULE = 'noModule';

    /**
     * @var ModuleInterface[]
     * [
     *  // path => ModuleInterface,
     *  '/'  => RootHandler,
     * ]
     */
    private $modules;

    /** @var  AppServer */
    private $server;

    /** @var array  */
    protected $options = [
        'debug' => false,

        'name' => 'application',

        // request and response data type: json text
        'dataType' => 'json',

        // allowed accessed Origins. e.g: [ 'localhost', 'site.com' ]
        'allowedOrigins' => '*',

        // for http

        // @link https://wiki.swoole.com/wiki/page/410.html
        'openGzip' => true,
        'gzipLevel' => 1, // allow 1 - 9
    ];

    /**
     * {@inheritDoc}
     */
    public function __construct(array $options = [])
    {
        if ($options) {
            $this->setOptions($options);
        }

        $this->init();
    }

    protected function init()
    {
        $timeZone = \Sws::get('config')->get('timeZone', 'UTC');

        date_default_timezone_set($timeZone);
    }

    /**
     * run
     */
    public function run()
    {
        $this->bootstrap();
    }

    /** @var array  */
    protected $httpMiddlewares = [];

    /**
     * @param callable $cb middleware :: (Context $ctx, $next) -> void
     * @return $this
     */
    public function use(callable $cb)
    {
        $this->httpMiddlewares[] = $cb;

        return $this;
    }

    public function bootstrap()
    {
        // collect routes
        $this->log('collected route count: ' . \Sws::get('httpRouter')->count());

        $this->log(sprintf(
            'registered services count: %d, services: %s',
            \Sws::$di->count(),
            \Sws::$di->getIds(false)
        ));

        $this->log(sprintf(
            'stored objects count: %d',
            Obj::count()
        ));

        // model class

        // controller class

        // rpc service class
    }

    /**
     * Returns the configuration of core application services.
     */
//    public function coreServices()
//    {
//        return [
//            'log' => ['target' => Logger::class],
//            'language' => ['target' => Language::class],
//
//            'httpRouter' => ['target' => ORouter::class],
//            'httpDispatcher' => ['target' => RouteDispatcher::class],
//
//            'rpcDispatcher' => ['target' => RpcDispatcher::class],
//        ];
//    }

    /*******************************************************************************
     * http handle
     ******************************************************************************/
    /**
     * {@inheritdoc}
     */
    protected function beforeRequest(SwRequest $request, SwResponse $response)
    {
        $request->server['request_memory'] = memory_get_usage();
        $uri = $request->server['request_uri'];

        Sws::info("The request [$uri] start. fd: {$request->fd}");
    }

    /**
     * @param SwRequest $swRequest
     * @param SwResponse $swResponse
     * @throws \Throwable
     */
    public function handleHttpRequest(SwRequest $swRequest, SwResponse $swResponse)
    {
        $this->beforeRequest($swRequest, $swResponse);

        /**
         * 当前请求的上下文对象
         * 包含：
         * - request 请求对象
         * - response 响应对象
         * - rid 本次请求的唯一ID(根据此ID 可以获取到原始的 swoole request)
         * - args 路由的参数信息
         * @var HttpContext $context
         */
        $context = HttpContext::make($swRequest, $swResponse);

        try {

            /** @var Sws\Web\HttpDispatcher $dispatcher */
            $dispatcher = $this->di->get('httpDispatcher');

            $uri = $swRequest->server['request_uri'];
            $method = $swRequest->server['request_method'];
            $info = [
                'context count' =>  Sws::getContextManager()->count(),
                'context ids' => Sws::getContextManager()->getIds(),
            ];

            Sws::info("begin dispatch URI: $uri, METHOD: $method, fd: {$swRequest->fd}", $info);

            Sws::profile('dispatch');
            $result = $dispatcher->dispatch(parse_url($uri, PHP_URL_PATH), $method, [$context]);
            Sws::profileEnd('dispatch');

            if (!$result instanceof Response) {
                $content = $result ?: 'NO CONTENT TO DISPLAY';

                $response = $context->getResponse();
                $response->getBody()->write(is_string($content) ? $content : json_encode($content));
            } else {
                $response = $result;
            }
        } catch (\Throwable $e) {
            $response = $this->handleHttpException($e, __METHOD__, $context);
        }

        $response->setHeader('Server', $this->getName() . '-http-server');

        Show::write([
            "Response Status: <info>{$response->getStatusCode()}</info>"
        ]);
        Show::aList($response->getHeaders(), 'Response Headers');

        $this->respondHttp($response, $swResponse);

        $this->afterRequest($swRequest, $swResponse);
    }

    /**
     * {@inheritDoc}
     */
    public function afterRequest(SwRequest $request, SwResponse $response)
    {
        $uri = $request->server['request_uri'];
        $info = [
//            'context count' =>  ContextManager::count(),
//            'context ids' => ContextManager::getIds(),
        ];
        Sws::trace('test trace1');

        if ($ctx = Sws::getContextManager()->del()) {
            $info['_context'] = [
                'ctxId' => $ctx->getId(),
                'ctxKey' => $ctx->getKey(),
            ];
        }

        Sws::trace('test trace');
        Sws::info("The request [$uri] end. fd: {$request->fd}", $info);

        $stat = PhpHelper::runtime($request->server['request_time_float'], $request->server['request_memory']);

        Sws::notice("request stat: runtime={$stat['runtime']} memory={$stat['memory']} peak-memory={$stat['peakMemory']}", $info);
    }

    /**
     * @param Response $response
     */
    public function beforeResponse(Response $response)
    {
    }

    /**
     * @param Response $response
     * @param SwResponse $swResponse
     * @return mixed
     */
    public function respondHttp(Response $response, SwResponse $swResponse)
    {
        $this->beforeResponse($response);

        // if open gzip
        if ($this->getOption('openGzip')) {
            $swResponse->gzip((int)$this->getOption('gzipLevel'));
        }

        // set http status
        $swResponse->status($response->getStatus());

        // set headers
        foreach ($response->getHeadersObject()->getLines() as $name => $value) {
            $swResponse->header($name, $value);
        }

        // set cookies
        foreach ($response->cookies->toHeaders() as $value) {
            $swResponse->header('Set-Cookie', $value);
        }

        // write content
        if ($body = (string)$response->getBody()) {
            $swResponse->write($body);
        }

        // send response to client
        $ret = $swResponse->end();

        $this->afterResponse($ret);

        return $ret;
    }

    /**
     * afterResponse. you can do some clear work
     * @param $ret
     */
    protected function afterResponse($ret)
    {
    }

    /**
     * @param \Throwable|\Exception $e
     * @param string $catcher
     * @param HttpContext $ctx
     * @return Response
     */
    public function handleHttpException($e, $catcher, HttpContext $ctx)
    {
        $resp = $ctx->getResponse();
        $html = PhpHelper::exceptionToHtml($e, $this->isDebug(), $catcher);

        // write error log
        Sws::error(strip_tags($html));

        if ($ctx->getRequest()->isAjax()) {
            $json = PhpHelper::exceptionToJson($e, $this->isDebug(), $catcher);
            $resp->setHeader('Content-Type', 'application/json; charset=utf-8');
            $resp->write($json);
        } else {
            $resp->setHeader('Content-Type', 'text/html; charset=utf-8');
            $resp->write($html);
        }

        return $resp;
    }

    /**
     * @param string $path
     * @param array $args
     * @param string $method
     * @throws \Throwable
     */
    public function subRequest($path, array $args = [], $method = 'GET')
    {
        $this->getContext()->setArgs($args);

        array_unshift($args, $this->getContext());

        /** @var Sws\Web\HttpDispatcher $dispatcher */
        $dispatcher = $this->di->get('httpDispatcher');
        $dispatcher->dispatch($path, $method, $args);
    }

    /*******************************************************************************
     * websocket handle
     ******************************************************************************/

    /**
     * webSocket 只会在连接握手时会有 request, response
     * @param Request $request
     * @param Response $response
     * @param int $cid
     * @return bool
     */
    public function handleHandshake(Request $request, Response $response, int $cid)
    {
        $path = $request->getPath();

        try {
            // check module. if not exists, response 404 error
            if (!$module = $this->getModule($path, false)) {
                Sws::error("The #$cid request's path [$path] route handler module not exists.");

                $this->fire(self::ON_NO_MODULE, [$cid, $path, $this]);

                $response
                    ->setStatus(404)
                    ->setHeaders(['Connection' => 'close'])
                    ->write("You request route path [$path] not found!");

                return false;
            }

            // check request
            if (!$module->validateRequest($request, $response)) {
                return false;
            }

            // application/json
            // text/plain
            $response->setHeader('Server', $this->getName() . '-websocket-server');
            // $response->setHeader('Access-Control-Allow-Origin', '*');

            $module->setApp($this)->onHandshake($request, $response);

            return true;
        } catch (\Throwable $e) {
            $response
                ->setStatus(500)
                ->setHeaders(['Connection' => 'close'])
                ->write('Error on handshake: ' . $e->getMessage());

            $error = PhpHelper::exceptionToString($e, 1, __METHOD__);
            Sws::error($error);

            return false;
        }
    }

    /**
     * @inheritdoc
     */
    public function handleWsMessage($server, Frame $frame, Connection $conn)
    {
        // dispatch command

        try {
            if ($module = $this->getModule($conn->getPath())) {
                $result = $module->dispatch($frame->data, $conn, $server);

                if ($result && is_string($result)) {
                    $this->server->send($result);
                }
            }
        } catch (\Throwable $e) {
            $this->handleWsException($e, $conn, __METHOD__);
        }
//        return;
    }

    /**
     * @param \Throwable|\Exception $e
     * @param Connection $conn
     * @param $catcher
     */
    public function handleWsException($e, Connection $conn, $catcher)
    {
        $error = PhpHelper::exceptionToString($e, 1, $catcher);

        Sws::error($error);

        $this->server->sendFormatted('', $e->getMessage(), __LINE__)->to($conn->getId())->send();
    }

    /*******************************************************************************
     * handle ws request route module
     ******************************************************************************/

    /**
     * register a route and it's handler module
     * @param string $path route path
     * @param ModuleInterface $module the route path module
     * @param bool $replace replace exists's route
     * @return ModuleInterface
     */
    public function addModule(string $path, ModuleInterface $module, $replace = false)
    {
        return $this->module($path, $module, $replace);
    }
    public function module(string $path, ModuleInterface $module, $replace = false)
    {
        $path = trim($path) ?: '/';
        $pattern = '/^\/[a-zA-Z][\w-]+$/';

        if ($path !== '/' && 1 !== preg_match($pattern, $path)) {
            throw new \InvalidArgumentException("The route path[$path] format must be match: $pattern");
        }

        if (!$replace && $this->hasModule($path)) {
            throw new \InvalidArgumentException("The route path[$path] have been registered!");
        }

        Sws::info("register the ws module for path: $path, module: {$module->getName()}, class: " . get_class($module));

        $this->modules[$path] = $module;

        return $module;
    }

    /**
     * @param $path
     * @return bool
     */
    public function hasModule(string $path): bool
    {
        return isset($this->modules[$path]);
    }

    /**
     * @param string $path
     * @param bool $throwError
     * @return ModuleInterface
     */
    public function getModule(string $path = '/', $throwError = true): ?ModuleInterface
    {
        if (!$this->hasModule($path)) {
            if ($throwError) {
                throw new \RuntimeException("The route handler not exists for the path: $path");
            }

            return null;
        }

        return $this->modules[$path];
    }

    /**
     * @return array
     */
    public function getModulePaths(): array
    {
        return array_keys($this->modules);
    }

    /**
     * @return array
     */
    public function getModules(): array
    {
        return $this->modules;
    }

    /**
     * @param array $modules
     */
    public function setModules(array $modules)
    {
        foreach ($modules as $route => $module) {
            $this->module($route, $module);
        }
    }

    /*******************************************************************************
     * a very simple's user storage
     ******************************************************************************/

    /**
     * @var array
     */
    private $users = [];

    public function getUser($index)
    {
        return $this->users[$index] ?? null;
    }

    public function userLogin($index, $data)
    {

    }

    public function userLogout($index, $data)
    {

    }

    /*******************************************************************************
     * helper method
     ******************************************************************************/

    /**
     * output log message
     * @param  string $msg
     * @param  array $data
     * @param string|int $level
     * @return void
     * @throws \RuntimeException
     */
    public function log($msg, array $data = [], $level = Logger::INFO)
    {
        $this->get('logger')->log($level, $msg, $data);
    }

    /**
     * @return bool
     */
    public function isJsonType(): bool
    {
        return $this->options['dataType'] === self::DATA_JSON;
    }

    /**
     * @return string
     */
    public function getDataType(): string
    {
        return $this->getOption('dataType');
    }

    /**
     * @return AppServer
     */
    public function getServer(): AppServer
    {
        return $this->server;
    }

    /**
     * @param AppServer $server
     */
    public function setServer(AppServer $server)
    {
        $this->server = $server;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->getOption('name');
    }

    /**
     * @return bool
     */
    public function isDebug(): bool
    {
        return (bool)Sws::get('config')->get('debug', false);
    }
}
