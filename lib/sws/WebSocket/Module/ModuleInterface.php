<?php
/**
 * Created by PhpStorm.
 * User: Inhere
 * Date: 2017/3/26 0026
 * Time: 15:35
 */

namespace Sws\WebSocket\Module;

use Monolog\Logger;
use Swoole\WebSocket\Server;
use Sws\Application;
use Inhere\Http\ServerRequest as Request;
use Inhere\Http\Response;
use Sws\WebSocket\Connection;
use Sws\WebSocket\Message;

/**
 * Interface ModuleInterface
 * @package Sws\WebSocket\Module
 */
interface ModuleInterface
{
    const SEND_PING = 'ping';
    const NOT_FOUND = 'notFound';
    const PARSE_ERROR = 'error';

    const DATA_JSON = 'json';
    const DATA_TEXT = 'text';

    /**
     * @param Request $request
     * @param Response $response
     * @return bool
     */
    public function validateRequest(Request $request, Response $response);

    /**
     * @param Request $request
     * @param Response $response
     */
    public function onHandshake(Request $request, Response $response);

    /**
     * @param int $id
     * @param Connection $conn
     */
    public function onOpen(int $id, Connection $conn);

    /**
     * @param int $id
     * @param Connection $conn
     */
    public function onClose(int $id, Connection $conn);

    /**
     * @param Application $app
     * @param string $msg
     */
    public function onError(Application $app, string $msg);

    public function checkIsAllowedOrigin(string $from);

    /**
     * @param string $data
     * @param Connection $conn
     * @param Server $server
     * @return mixed
     */
    public function dispatch(string $data, Connection $conn, Server $server);

    /**
     * @param string $command
     * @param $handler
     * @return static
     */
    public function add(string $command, $handler);

    public function log(string $message, array $data = [], $level = Logger::INFO);

    public function isJsonType();

    /**
     * @param $data
     * @param string $msg
     * @param int $code
     * @param bool $doSend
     * @return int|Message
     */
    public function respond($data, string $msg = 'success', int $code = 0, bool $doSend = true);

    /**
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public function getOption(string $key, $default = null);

    /**
     * @param Application $app
     * @return static
     */
    public function setApp(Application $app);

    /**
     * @return Application
     */
    public function getApp(): Application;

    public function getName(): string;
}
