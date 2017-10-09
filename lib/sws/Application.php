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
use Sws\Components\HttpHelper;
use Sws\Context\ContextGetTrait;
use Sws\Context\ContextManager;
use Sws\Context\HttpContext;
use Sws\Module\ModuleInterface;
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
    use ContextGetTrait;

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
        $this->server->handleDynamicRequest([$this, 'handleHttpRequest']);

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
     * @param SwRequest $swRequest
     * @param SwResponse $swResponse
     * @param string|null $uri
     * @return SwResponse
     * @throws \Throwable
     */
    public function handleHttpRequest(SwRequest $swRequest, SwResponse $swResponse, $uri = null)
    {
        try {
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
            /** @var Sws\Web\HttpDispatcher $dispatcher */
            $dispatcher = $this->di->get('httpDispatcher');

            $uri = $uri ?: $swRequest->server['request_uri'];
            $method = $swRequest->server['request_method'];
            $info = [
                'context count' =>  ContextManager::count(),
                'context ids' => ContextManager::getIds(),
            ];

            Sws::info("begin dispatch URI: $uri, METHOD: $method, fd: {$swRequest->fd}, ctxId: {$context->getId()}, ctxKey: {$context->getKey()}", $info);

            $result = $dispatcher->dispatch(parse_url($uri, PHP_URL_PATH), $method, [$context]);

            if (!$result instanceof Response) {
                $response = $context->getResponse();
                $response->getBody()->write((string)$result);
            } else {
                $response = $result;
            }
        } catch (\Throwable $e) {
            $response = $this->handleHttpException($e, __METHOD__);
        }

        $response->setHeader('Server', $this->getName() . '-http-server');

        Show::write([
            "Response Status: <info>{$response->getStatusCode()}</info>"
        ]);
        Show::aList($response->getHeaders(), 'Response Headers');
//        Show::aList($_SESSION ?: [],'server sessions');

        return HttpHelper::paddingSwResponse($response, $swResponse);
    }

    /**
     * @param \Throwable $e
     * @param string $catcher
     * @return Response
     */
    protected function handleHttpException(\Throwable $e, $catcher)
    {
        $error = PhpHelper::exceptionToString($e, $this->isDebug(), $catcher);

        Sws::error($error);

        return $this->getResponse()->write($error);
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

        // check module. if not exists, response 404 error
        if (!$module = $this->getModule($path, false)) {
            Sws::error("The #$cid request's path [$path] route handler not exists.");

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
            throw $e;
        }
//        return;
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
