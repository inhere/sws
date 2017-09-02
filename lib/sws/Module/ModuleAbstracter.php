<?php
/**
 * Created by PhpStorm.
 * User: Inhere
 * Date: 2017/3/27 0027
 * Time: 22:51
 */

namespace Sws\Module;

use inhere\library\helpers\PhpHelper;
use inhere\library\traits\OptionsTrait;
use Sws\Application;
use Sws\WebSocket\Connection;
use Sws\DataParser\ComplexDataParser;
use Sws\DataParser\DataParserInterface;
use Sws\Http\Request;
use Sws\Http\Response;
use Sws\Http\WSResponse;

/**
 * Class ARouteHandler
 * @package Sws\Module
 */
abstract class ModuleAbstracter implements ModuleInterface
{
    use OptionsTrait;

    // custom ws handler position
    const OPEN_HANDLER = 0;
    const MESSAGE_HANDLER = 1;
    const CLOSE_HANDLER = 2;
    const ERROR_HANDLER = 3;

    /**
     * the module name
     * @var string
     */
    private $name;

    /**
     * @var Application
     */
    private $app;

    /**
     * @var Request
     */
    private $request;

    /**
     * @var DataParserInterface
     */
    private $_dataParser;

    /**
     * @var array
     * [
     *   cmd => callback,
     * ]
     */
    protected $cmdHandlers = [];

    // default command name, if request data not define command name.
    const DEFAULT_CMD = 'index';
    const DEFAULT_CMD_SUFFIX = 'Command';

    const DENY_ALL = '!';
    const ALLOW_ALL = '*';

    /**
     * @var array
     */
    protected $options = [
        // request and response data type: json text
        'dataType' => 'json',

        // It is valid when `'dataType' => 'json'`, allow: 1 raw 2 array 3 object
        'jsonParseTo' => DataParserInterface::JSON_TO_ARRAY,

        // default command name, if request data not define command name.
        'defaultCmd' => self::DEFAULT_CMD,
        // default command suffix
        'cmdSuffix' => self::DEFAULT_CMD_SUFFIX,

        // allowed request Origins. e.g: [ 'localhost', 'site.com' ]
        'allowedOrigins' => '*',
    ];

    /**
     * ARouteHandler constructor.
     * @param string $name
     * @param array $options
     * @param DataParserInterface|null $dataParser
     */
    public function __construct($name = null, array $options = [], DataParserInterface $dataParser = null)
    {
        $this->setOptions($options);

        $this->_dataParser = $dataParser;
        $this->name = $name ?: $this->parseName();

        $this->init();
    }

    protected function init()
    {
    }

    /**
     * @inheritdoc
     */
    public function onHandshake(Request $request, Response $response)
    {
        $this->log(sprintf(
            'A new user connection. join the path(route): %s, module: %s',
            $request->getPath(),
            $this->name
        ));
    }

    /**
     * @inheritdoc
     */
    public function onOpen(int $cid, Connection $conn)
    {
        $this->log(sprintf(
            'A new user open connection. route path: %s, module: %s',
            $conn->getPath(),
            $this->name
        ));
    }

    /**
     * @inheritdoc
     */
    public function onClose(int $cid, Connection $conn)
    {
        $this->log(sprintf(
            'A user has been disconnected. Path: %s, module: %s',
            $conn->getPath(),
            $this->name
        ));
    }

    /**
     * @inheritdoc
     */
    public function onError(Application $app, string $msg)
    {
        $this->log('Accepts a connection on a socket error, when request : ' . $msg, [],'error');
    }

    /*******************************************************************************
     * handler http request
     ******************************************************************************/

    /**
     * @param Request $request
     * @param Response $response
     * @return bool
     */
    public function validateRequest(Request $request, Response $response)
    {
        $cid = $request->getAttribute('fd');
        $origin = $request->getOrigin();

        // check `Origin`
        // Access-Control-Allow-Origin: *
        if (!$this->checkIsAllowedOrigin($origin)) {
            $this->log("The #$cid Origin [$origin] is not in the 'allowedOrigins' list.", 'error');

            $response
                ->setStatus(403)
                ->setHeaders(['Connection' => 'close'])
                ->setBodyContent('Deny Access!');

            return false;
        }

        return true;
    }

    /**
     * check client is allowed origin
     * `Origin: http://foo.example`
     * @param string $from \
     * @return bool
     */
    public function checkIsAllowedOrigin(string $from)
    {
        $allowed = $this->getOption('allowedOrigins');

        // deny all
        if (!$allowed) {
            return false;
        }

        // allow all
        if (is_string($allowed) && $allowed === self::ALLOW_ALL) {
            return true;
        }

        if (!$from) {
            return false;
        }

        $allowed = (array)$allowed;

        return true;
    }

    /*******************************************************************************
     * handler message request
     ******************************************************************************/

    /**
     * parse and dispatch message command
     * @param string $data
     * @param Connection $conn
     * @return mixed
     */
    public function dispatch(string $data, Connection $conn)
    {
        $name = $this->name;
        $cid = $conn->getId();
        $route = $conn->getPath();

        // parse: get command and real data
        if ($results = $this->getDataParser()->parse($data, $cid, $this)) {
            list($command, $data) = $results;
            $command = $command ?: $this->getOption('defaultCmd') ?? self::DEFAULT_CMD;
            $this->log("The #{$cid} request command is: $command, in route: $route, module: $name, handler: " . static::class);
        } else {
            $command = self::PARSE_ERROR;
            $this->log("The #{$cid} request data parse failed in route: $route, module: $name. Data: $data", [], 'error');
        }

        // dispatch command

        // is a outside command `by add()`
        if ($this->isCommandName($command)) {
            $handler = $this->getCmdHandler($command);

            return PhpHelper::call($handler, [$data, $cid, $conn]);
        }

        $suffix = 'Command';
        $method = $command . $suffix;

        // not found
        if (!method_exists($this, $method)) {
            $this->log("The #{$cid} request command: $command not found, module: $name, run 'notFound' command", [],'notice');
            $method = self::NOT_FOUND . $suffix;
        }

        return $this->$method($data, $cid, $conn);
    }

    /**
     * register a command handler
     * @param string $command
     * @param callable $handler
     * @return ModuleInterface
     */
    public function command(string $command, callable $handler)
    {
        return $this->add($command, $handler);
    }

    /**
     * @param string $command
     * @param $handler
     * @return $this
     */
    public function add(string $command, $handler)
    {
        if ($command && preg_match('/^[a-z][\w-]+$/', $command)) {
            $this->cmdHandlers[$command] = $handler;
        }

        return $this;
    }

    /**
     * @param $data
     * @param int $cid
     * @return int
     */
    public function pingCommand(string $data, int $cid)
    {
        return $this->respondText($data . '+PONG', false)->to($cid)->send();
    }

    /**
     * @param $data
     * @param int $cid
     * @return int
     */
    public function errorCommand(string $data, int $cid)
    {
        return $this
            ->respond($data, 'you send data format is error!', -200, false)
            ->to($cid)
            ->send();
    }

    /**
     * @param string $command
     * @param int $cid
     * @param Connection $conn
     * @return int
     */
    public function notFoundCommand(string $command, int $cid, Connection $conn)
    {
        $msg = "You request command [$command] not found in the route [{$conn->getPath()}].";

        return $this->respond('', $msg, -404, false)->to($cid)->send();
    }

    /**
     * @param string $command
     * @return bool
     */
    public function isCommandName(string $command): bool
    {
        return array_key_exists($command, $this->cmdHandlers);
    }

    /**
     * @return array
     */
    public function getCommands(): array
    {
        return array_keys($this->cmdHandlers);
    }

    /**
     * @param string $command
     * @return callable|null
     */
    public function getCmdHandler(string $command)//: ?callable
    {
        if (!$this->isCommandName($command)) {
            return null;
        }

        return $this->cmdHandlers[$command];
    }

    /**
     * @return array
     */
    public function getCmdHandlers(): array
    {
        return $this->cmdHandlers;
    }

    /**
     * @param array $cmdHandlers
     */
    public function setCmdHandlers(array $cmdHandlers)
    {
        foreach ($cmdHandlers as $name => $handler) {
            $this->add($name, $handler);
        }
    }

    /////////////////////////////////////////////////////////////////////////////////////////
    /// helper method
    /////////////////////////////////////////////////////////////////////////////////////////

    /**
     * @return string
     */
    protected function parseName()
    {
        $className = $fullClass = trim(static::class, '\\');

        if (strpos($fullClass, '\\')) {
            $className = basename(str_replace('\\', '/', $fullClass));
        }

        $name = $className;

        if (strpos($name, 'Module')) {
            $name = substr($className, 0, -4) ?: $className;
        }

        return $name;
    }

    /**
     * @param $data
     * @param string $msg
     * @param int $code
     * @param bool $doSend
     * @return int|WSResponse
     */
    public function respond($data, string $msg = 'success', int $code = 0, bool $doSend = true)
    {
        return $this->app->wsRespond($data, $msg, $code, $doSend);
    }

    /**
     * @param $data
     * @param bool $doSend
     * @return WSResponse|int
     */
    public function respondText($data, bool $doSend = true)
    {
        return $this->app->respondText($data, $doSend);
    }

    public function send($data, string $msg = '', int $code = 0, \Closure $afterMakeMR = null, bool $reset = true): int
    {
        return $this->app->send($data, $msg, $code, $afterMakeMR, $reset);
    }

    public function sendText($data, \Closure $afterMakeMR = null, bool $reset = true)
    {
        return $this->app->sendText($data, $afterMakeMR, $reset);
    }

    public function log(string $message, array $data = [], string $type = 'info')
    {
        $this->app->log($message, $data, $type);
    }

    /////////////////////////////////////////////////////////////////////////////////////////
    /// getter/setter method
    /////////////////////////////////////////////////////////////////////////////////////////

    /**
     * @return bool
     */
    public function isJsonType(): bool
    {
        return $this->getOption('dataType') === self::DATA_JSON;
    }

    /**
     * @return string
     */
    public function getDataType(): string
    {
        return $this->getOption('dataType');
    }

    /**
     * @return DataParserInterface
     */
    public function getDataParser(): DataParserInterface
    {
        // if not set, use default parser.
        return $this->_dataParser ?: new ComplexDataParser();
    }

    /**
     * @param DataParserInterface $dataParser
     */
    public function setDataParser(DataParserInterface $dataParser)
    {
        $this->_dataParser = $dataParser;
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
     * @return $this|static
     */
    public function setApp(Application $app)
    {
        $this->app = $app;

        return $this;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name)
    {
        $this->name = $name;
    }
}
