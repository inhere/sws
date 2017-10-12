<?php
/**
 * Created by PhpStorm.
 * User: inhere
 * Date: 2017/9/1
 * Time: 下午7:02
 */

namespace Sws\WebSocket;

use Inhere\Library\Traits\ArrayAccessByPropertyTrait;
use Inhere\Library\Traits\PropertyAccessByGetterSetterTrait;

/**
 * Class Message
 * @package Sws\WebSocket
 */
final class Message implements \ArrayAccess
{
    use ArrayAccessByPropertyTrait;
    use PropertyAccessByGetterSetterTrait;

    /**
     * @var WebSocketServerInterface
     */
    private $ws;

    /**
     * the sender id
     * @var int
     */
    private $sender;

    /**
     * the receivers id list
     * @var array
     */
    private $receivers;

    /**
     * the excepted id list
     * @var array
     */
    private $excepted;

    /**
     * @var string
     */
    private $data;

    /**
     * @param string $data
     * @param array $receivers
     * @param array $excepted
     * @param int $sender
     * @return Message
     */
    public static function make(string $data = '', array $receivers = [], array $excepted = [], int $sender = 0)
    {
        return new self($data, $receivers, $excepted, $sender);
    }

    /**
     * Message constructor.
     * @param string $data
     * @param int $sender
     * @param array $receivers
     * @param array $excepted
     */
    public function __construct(string $data = '', array $receivers = [], array $excepted = [], int $sender = 0)
    {
        $this->data = $data;
        $this->sender = $sender;
        $this->receivers = $receivers;
        $this->excepted = $excepted;
    }

    /**
     * mark message sent
     * @var bool
     */
    private $_sent = false;

    /**
     * last status code
     * @var int
     */
    private $_status = 0;

    /**
     * @param bool $reset
     * @return int
     */
    public function send($reset = true)
    {
        if (!$this->ws) {
            throw new \InvalidArgumentException('Please set the property [ws], is instance of the WsServerInterface');
        }

        // if message have been sent, stop and return last status code
        if ($this->isSent()) {
            return $this->_status;
        }

        $status = $this->ws->send($this->getData(), $this->receivers, $this->excepted, $this->sender);

        if ($reset) {
            $this->reset();
        }

        // mark message have been sent
        $this->_sent = true;
        $this->_status = $status;

        return $status;
    }

    /**
     * reset
     */
    public function reset()
    {
        $this->_sent = false;
        $this->sender = $this->_status = 0;
        $this->receivers = $this->excepted = $this->data = [];

        return $this;
    }

    public function __destruct()
    {
        $this->reset();
    }

    /**
     * @return bool
     */
    public function isSent(): bool
    {
        return $this->_sent;
    }

    /**
     * @param bool $sent
     */
    public function setSent(bool $sent)
    {
        $this->_sent = $sent;
    }

    /**
     * @return int
     */
    public function getSender(): int
    {
        return $this->sender;
    }

    /**
     * @param int $sender
     * @return $this
     */
    public function sender(int $sender)
    {
        return $this->setSender($sender);
    }

    public function from(int $sender)
    {
        return $this->setSender($sender);
    }

    public function setSender(int $sender)
    {
        $this->sender = $sender;

        return $this;
    }

    /**
     * @return array
     */
    public function getReceivers(): array
    {
        return $this->receivers;
    }

    /**
     * @param int $cid
     * @return $this
     */
    public function receiver(int $cid)
    {
        return $this->addReceiver($cid);
    }

    public function addReceiver(int $cid)
    {
        if (!in_array($cid, $this->receivers, true)) {
            $this->receivers[] = $cid;
        }

        return $this;
    }

    /**
     * @param array|int $receivers
     * @return $this
     */
    public function to($receivers)
    {
        return $this->setReceivers($receivers);
    }

    public function setReceivers($receivers)
    {
        $this->receivers = (array)$receivers;

        return $this;
    }

    /**
     * @return array
     */
    public function getExcepted(): array
    {
        return $this->excepted;
    }

    /**
     * @param $receiver
     * @return $this
     */
    public function except(int $receiver)
    {
        if (!in_array($receiver, $this->excepted, true)) {
            $this->excepted[] = $receiver;
        }

        return $this;
    }

    /**
     * @param array|int $excepted
     * @return $this
     */
    public function setExcepted($excepted)
    {
        $this->excepted = (array)$excepted;

        return $this;
    }

    /**
     * @param string $data
     * @param bool $toLast
     * @return $this
     */
    public function addData(string $data, bool $toLast = true)
    {
        if ($toLast) {
            $this->data .= $data;
        } else {
            $this->data = $data . $this->data;
        }

        return $this;
    }

    /**
     * @return string
     */
    public function getData(): string
    {
        return $this->data;
    }

    /**
     * @param string $data
     * @return self
     */
    public function setData(string $data)
    {
        $this->data = $data;

        return $this;
    }

    /**
     * @return WebSocketServerInterface
     */
    public function getWs(): WebSocketServerInterface
    {
        return $this->ws;
    }

    /**
     * @param WebSocketServerInterface $ws
     * @return $this
     */
    public function setWs(WebSocketServerInterface $ws)
    {
        $this->ws = $ws;

        return $this;
    }

    /**
     * @return int
     */
    public function getStatus(): int
    {
        return $this->_status;
    }

    /**
     * @param int $status
     */
    public function setStatus(int $status)
    {
        $this->_status = $status;
    }
}

