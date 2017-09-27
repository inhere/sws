<?php
/**
 * Created by PhpStorm.
 * User: inhere
 * Date: 2017-09-25
 * Time: 16:45
 */

namespace Sws\Async;

use Monolog\Handler\AbstractProcessingHandler;
use Monolog\Logger;
use Swoole\Async;
use function Sws\app;

/**
 * Class StreamHandler
 * @package Sws\Async
 */
class StreamHandler extends AbstractProcessingHandler
{
    const SPLIT_NO = 0;
    const SPLIT_DAY = 1;
    const SPLIT_HOUR = 2;

    protected $stream;
    protected $file;
    private $errorMessage;
    protected $filePermission;

    private $dirCreated;

    public $onWriteEnd;

    /** @var int  */
    private $splitType;

    /**
     * @param resource|string $stream
     * @param bool|int $level The minimum logging level at which this handler will be triggered
     * @param int|null $filePermission Optional file permissions (default (0644) are only for owner read/write)
     * @param int $splitType
     */
    public function __construct($stream, $level = Logger::DEBUG, $splitType = self::SPLIT_NO, $filePermission = null)
    {
        parent::__construct($level);

        if (is_resource($stream)) {
            $this->stream = $stream;
        } elseif (is_string($stream)) {
            $this->file = $stream;
        } else {
            throw new \InvalidArgumentException('A stream must either be a resource or a string.');
        }

        $this->filePermission = $filePermission;
        $this->splitType = $splitType;

        // fix it
        if ($this->splitType && !in_array($this->splitType, [self::SPLIT_DAY, self::SPLIT_HOUR], true)) {
            $this->splitType = self::SPLIT_DAY;
        }

    }

    /**
     * {@inheritdoc}
     */
    protected function write(array $record)
    {
        if (null === $this->file || '' === $this->file) {
            throw new \LogicException('Missing stream url, the stream can not be opened. This may be caused by a premature call to close().');
        }
        $this->createDir();
        $this->errorMessage = null;

        $info = pathinfo($this->file);
        $dir = $info['dirname'];
        $name = $info['filename'] ?? 'unknown';
        $ext = $info['extension'] ?? 'log';
        $sfx = $this->getFileSuffix();

        $file = "{$dir}/{$name}_{$sfx}.{$ext}";

        set_error_handler(array($this, 'customErrorHandler'));
        if ($this->filePermission !== null) {
            @chmod($file, $this->filePermission);
        }
        restore_error_handler();

        if (!app() || !app()->isBootstrapped() || app()->isTaskWorker()) {
            file_put_contents($file, (string) $record['formatted'], FILE_APPEND);
        } else {
            Async::writeFile($file, (string) $record['formatted'], $this->onWriteEnd, FILE_APPEND);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function close()
    {
        if ($this->file && is_resource($this->stream)) {
            fclose($this->stream);
        }
        $this->stream = null;
    }

    /**
     * Return the stream URL if it was configured with a URL and not an active resource
     *
     * @return string|null
     */
    public function getFile()
    {
        return $this->file;
    }

    private function customErrorHandler($code, $msg)
    {
        $this->errorMessage = preg_replace('{^(fopen|mkdir)\(.*?\): }', '', $msg);
    }

    /**
     * @param string $stream
     *
     * @return null|string
     */
    private function getDirFromStream($stream)
    {
        $pos = strpos($stream, '://');
        if ($pos === false) {
            return dirname($stream);
        }

        if (0 === strpos($stream, 'file://')) {
            return dirname(substr($stream, 7));
        }

        return null;
    }

    private function createDir()
    {
        // Do not try to create dir if it has already been tried.
        if ($this->dirCreated) {
            return;
        }

        $dir = $this->getDirFromStream($this->file);
        if (null !== $dir && !is_dir($dir)) {
            $this->errorMessage = null;
            set_error_handler(array($this, 'customErrorHandler'));
            $status = mkdir($dir, 0777, true);
            restore_error_handler();
            if (false === $status) {
                throw new \UnexpectedValueException(sprintf('There is no existing directory at "%s" and its not buildable: '.$this->errorMessage, $dir));
            }
        }
        $this->dirCreated = true;
    }


    protected function fileIsChanged()
    {
        if (!$this->splitType) {
            return false;
        }

        $str = '';

        if ($this->splitType === self::SPLIT_DAY) {
            $str = date('Ymd');
        } elseif ($this->splitType === self::SPLIT_HOUR) {
            $str = date('Ymd_H');
        }

        return !strpos($this->file, '_' . $str);
    }

    /**
     * @return string
     */
    public function getFileSuffix()
    {
        $str = '';

        if ($this->splitType === self::SPLIT_DAY) {
            $str = date('Ymd');
        } elseif ($this->splitType === self::SPLIT_HOUR) {
            $str = date('Ymd_H');
        }

        return $str;
    }

}