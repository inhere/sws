<?php
/**
 * Created by PhpStorm.
 * User: inhere
 * Date: 2017-09-28
 * Time: 14:01
 */

namespace Sws;

use Inhere\Console\Utils\Show;
use Inhere\Http\Request;
use Inhere\Http\Response;
use Inhere\Server\Components\StaticResourceProcessor;
use Inhere\Server\Servers\HttpServer;
use Swoole\Http\Request as SwRequest;
use Swoole\Http\Response as SwResponse;
use Swoole\WebSocket\Frame;
use Sws\Context\ContextManager;
use Sws\WebSocket\Connection;
use Sws\WebSocket\Message;
use Sws\WebSocket\WebSocketServerTrait;
use Sws\WebSocket\WsServerInterface;

/**
 * Class AppServer
 * @package Sws
 * @property \Swoole\Server $server
 */
final class AppServer extends HttpServer implements WsServerInterface
{
    use WebSocketServerTrait;

    /** @var  Application */
    private $app;

    /**
     * {@inheritDoc}
     */
    protected function beforeRun()
    {
        // 捕获异常
        register_shutdown_function([$this, 'handleFatal']);
        set_error_handler([$this, 'handleError']);
        set_exception_handler([$this, 'handleException']);
    }

    /**
     * before Server Start
     */
    protected function beforeServerStart()
    {
        $config = \Sws::$di->get('config')->get('assets', []);

        // static handle
        $this->staticAccessHandler = new StaticResourceProcessor(BASE_PATH, $config['ext'], $config['dirMap']);
    }

    /**
     * {@inheritdoc}
     */
    protected function beforeRequest(SwRequest $request, SwResponse $response)
    {
        $uri = $request->server['request_uri'];

        $this->log("The request [$uri] start. fd: {$request->fd}");
    }

    /**
     * {@inheritDoc}
     */
    public function afterRequest(SwRequest $request, SwResponse $response)
    {
        $uri = $request->server['request_uri'];
        $info = [
            'context count' =>  ContextManager::count(),
            'context ids' => ContextManager::getIds(),
        ];

        if ($ctx = ContextManager::delContext()) {
            $this->log("The request [$uri] end. fd: {$request->fd}, ctxId: {$ctx->getId()}, ctxKey: {$ctx->getKey()}", $info);
        } else {
            $this->log("The request [$uri] end. fd: {$request->fd}. context info has lost!", $info);
        }
    }

    /**
     * webSocket 只会在连接握手时会有 request, response
     * @param Request $request
     * @param Response $response
     * @param int $cid
     * @return bool
     */
    public function handleHandshake(Request $request, Response $response, int $cid)
    {
        return $this->app->handleHandshake($request, $response, $cid);
    }

    /**
     * @inheritdoc
     */
    protected function handleWsMessage($server, Frame $frame, Connection $conn)
    {
        $cid = $frame->fd;

        if (!$conn) {
            $this->log("The connection #{$cid} has lost, meta: \n" . var_export($conn->all(), 1));
            $this->close($cid);

            return;
        }

        $this->app->handleWsMessage($server, $frame, $conn);
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
            'time' => microtime(true),
        ]);
    }

    /**
     * @param $data
     * @param string $msg
     * @param int $code
     * @return string
     */
    public function formatMessage($data, string $msg = 'success', int $code = 0)
    {
        // json
        if ($this->app->isJsonType()) {
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
     * @param string $data
     * @param int $sender
     * @param array $receivers
     * @param array $excepted
     * @return Message
     */
    public function createMessage(string $data = '', int $sender = 0, array $receivers = [], array $excepted = [])
    {
        return Message::make($data, $sender, $receivers, $excepted)->setWs($this);
    }

    /**
     * response data to client, will auto build formatted message by 'data_type'
     * @param mixed $data
     * @param string $msg
     * @param int $code
     * @param bool $doSend
     * @return int|Message
     */
    public function wsRespond($data, string $msg = '', int $code = 0, bool $doSend = true)
    {
        $data = $this->formatMessage($data, $msg, $code);

        return $this->respondText($data, $doSend);
    }

    /**
     * response text data to client
     * @param $data
     * @param bool $doSend
     * @return int|Message
     */
    public function respondText($data, bool $doSend = true)
    {
        if (is_array($data)) {
            $data = implode('', $data);
        }

        $wrs = Message::make($data)->setWs($this);

        if ($doSend) {
            $wrs->send();
        }

        return $wrs;
    }

    /**
     * @param string $data
     * @param string $msg
     * @param int $code
     * @return Message
     */
    public function sendFormatted($data, string $msg = '', int $code = 0)
    {
        $response = $this->formatMessage($data, $msg, $code);

        return Message::make($response)->setWs($this);
    }

    /**
     * response text data to client
     * @param $data
     * @return Message
     */
    public function sendText($data)
    {
        if (is_array($data)) {
            $data = implode('', $data);
        }

        return $this->createMessage($data);
    }

    /**
     * {@inheritdoc}
     */
    public function showHelp($scriptName, $quit = false)
    {
        $logo = <<<LOGO
       _____
      / ___/      _______
      \__ \ | /| / / ___/
     ___/ / |/ |/ (__  )
    /____/|__/|__/____/
LOGO;

        Show::write("<info>$logo</info> powered by php 7,swoole 2\n");

        return parent::showHelp($scriptName, $quit);
    }

    /**
     * @return Application
     */
    public function getApp(): Application
    {
        return $this->app;
    }

    /**
     * @param Application $app
     */
    public function setApp(Application $app)
    {
        $this->app = $app;
    }
}
