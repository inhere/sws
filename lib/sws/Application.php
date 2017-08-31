<?php
/**
 * Created by PhpStorm.
 * User: inhere
 * Date: 2017-08-25
 * Time: 9:40
 */

namespace Sws;

use inhere\console\utils\Show;
use inhere\library\di\Container;

use inhere\sroute\ORouter;
use Swoole\Server;
use Sws\Components\HttpHelper;
use Sws\Http\Request;
use Sws\Http\Response;
use Sws\Http\WSResponse;
use Sws\Module\ModuleInterface;
use Sws\Module\RootModule;
use Sws\Server\WebSocketServer;
use Sws\Server\WsServerInterface;

use Swoole\Websocket\Frame;
use Swoole\Http\Request as SwRequest;
use Swoole\Http\Response as SwResponse;

/**
 * Class Application
 * @package Sws
 */
class Application extends WebSocketServer implements WsServerInterface
{
    const DATA_JSON = 'json';
    const DATA_TEXT = 'text';

    /**
     * @var Container
     */
    private $di;

    /**
     * @var ModuleInterface[]
     * [
     *  // path => ModuleInterface,
     *  '/'  => RootHandler,
     * ]
     */
    private $modules;

    public function run()
    {
        return parent::run();
    }

    /**
     * @inheritdoc
     */
    public function start($daemon = null)
    {
        // if not register route, add a default root path module handler
        if (0 === count($this->modules)) {
            $this->module('/', new RootModule());
        }

        $this->handleDynamicRequest([$this, 'handleHttpRequest']);

        parent::start($daemon);
    }

    /**
     * @param SwRequest $swRequest
     * @param SwResponse $swResponse
     * @return SwResponse
     * @throws \Throwable
     */
    public function handleHttpRequest(SwRequest $swRequest, SwResponse $swResponse)
    {
        /** @var Request $request */
        $request = HttpHelper::createRequest($swRequest);
        $response = HttpHelper::createResponse();

        /** @var ORouter $router */
        $router = $this->di->get('router');

        try {
            $method = $swRequest->server['request_method'];
            $uri = $swRequest->server['request_uri'];
            $resp = $router->dispatch(null, parse_url($uri, PHP_URL_PATH), $method);

            if (!$resp instanceof Response) {
                $response->getBody()->write((string)$resp);
            }

            HttpHelper::paddingSwooleResponse($response, $swResponse);
        } catch (\Throwable $e) {
            throw $e;
        }

        Show::write([
            "Response Status: <info>{$response->getStatusCode()}</info>"
        ]);
        Show::aList($response->getHeaders(), 'Response Headers');
        Show::aList($_SESSION ?: [],'server sessions');

        return $swResponse;
    }

    /**
     * @param Server $server
     * @param Frame $frame
     */
    public function handleWsRequest($server, Frame $frame)
    {
        $cid = $frame->fd;
        $meta = $this->getConnection($cid);

        if (!$meta) {
            $this->log("The connection #{$cid} has lost.");
            $this->close($cid);

            return;
        }

        // dispatch command

        // $path = $ws->getClient($cid)['path'];
        $result = $this->getModule($meta['path'])->dispatch($frame->data, $cid);

        if ($result && is_string($result)) {
            $this->send($result);
        }

//        return;
    }

    /////////////////////////////////////////////////////////////////////////////////////////
    /// handle request route module
    /////////////////////////////////////////////////////////////////////////////////////////

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

        if ($path !== '/' && preg_match($pattern, $path)) {
            throw new \InvalidArgumentException("The route path format must be match: $pattern");
        }

        if (!$replace && $this->hasModule($path)) {
            throw new \InvalidArgumentException("The route path [$path] have been registered!");
        }

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
    public function getModule(string $path = '/', $throwError = true): ModuleInterface
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

    /////////////////////////////////////////////////////////////////////////////////////////
    /// response
    /////////////////////////////////////////////////////////////////////////////////////////

    /**
     * @param string $data
     * @param string $msg
     * @param int $code
     * @return string
     */
    public function fmtJson($data, string $msg = 'success', int $code = 0): string
    {
        return json_encode([
            'data' => $data,
            'msg' => $msg,
            'code' => $code,
            'time' => time(),
        ]);
    }

    /**
     * @param $data
     * @param string $msg
     * @param int $code
     * @return string
     */
    public function buildMessage($data, string $msg = 'success', int $code = 0)
    {
        // json
        if ($this->isJsonType()) {
            $data = $this->fmtJson($data, $msg ?: 'success', $code);

            // text
        } else {
            if ($data && is_array($data)) {
                $data = json_encode($data);
            }

            $data = $data ?: $msg;
        }

        return $data;
    }

    /**
     * response data to client, will auto build formatted message by 'data_type'
     * @param mixed $data
     * @param string $msg
     * @param int $code
     * @param bool $doSend
     * @return int|WSResponse
     */
    public function wsRespond($data, string $msg = '', int $code = 0, bool $doSend = true)
    {
        $data = $this->buildMessage($data, $msg, $code);

        return $this->respondText($data, $doSend);
    }

    /**
     * response text data to client
     * @param $data
     * @param bool $doSend
     * @return int|WSResponse
     */
    public function respondText($data, bool $doSend = true)
    {
        if (is_array($data)) {
            $data = implode('', $data);
        }

        $wrs = WSResponse::make($data)->setWs($this);

        if ($doSend) {
            $wrs->send();
        }

        return $wrs;
    }

    /**
     * @param $data
     * @param string $msg
     * @param int $code
     * @param \Closure|null $afterMakeMR
     * @param bool $reset
     * @return int
     */
    public function send($data, string $msg = '', int $code = 0, \Closure $afterMakeMR = null, bool $reset = true): int
    {
        $data = $this->buildMessage($data, $msg, $code);

        return $this->sendText($data, $afterMakeMR, $reset);
    }

    /**
     * response text data to client
     * @param $data
     * @param \Closure|null $onAfterMake
     * @param bool $reset
     * @return int
     */
    public function sendText($data, \Closure $onAfterMake = null, bool $reset = true)
    {
        if (is_array($data)) {
            $data = implode('', $data);
        }

        $wrs = WSResponse::make($data)->setWs($this);

        if ($onAfterMake) {
            $status = $onAfterMake($wrs);

            // If the message have been sent
            if (is_int($status)) {
                return $status;
            }
        }

        return $wrs->send($reset);
    }

    /////////////////////////////////////////////////////////////////////////////////////////
    /// a very simple's user storage
    /////////////////////////////////////////////////////////////////////////////////////////

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

    /////////////////////////////////////////////////////////////////////////////////////////
    /// helper method
    /////////////////////////////////////////////////////////////////////////////////////////

    /**
     * @return bool
     */
    public function isJsonType(): bool
    {
        return $this->getOption('data_type') === self::DATA_JSON;
    }

    /**
     * @return string
     */
    public function getDataType(): string
    {
        return $this->getOption('data_type');
    }

    /**
     * @return Container
     */
    public function getDi(): Container
    {
        return $this->di;
    }

    /**
     * @param Container $di
     */
    public function setDi(Container $di)
    {
        $this->di = $di;
    }
}