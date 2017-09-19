<?php
/**
 * Created by PhpStorm.
 * User: inhere
 * Date: 2017-09-12
 * Time: 17:30
 */

namespace Sws\Annotations;

use inhere\library\files\FileFinder;

/**
 * Class Collector
 * @package Sws\Annotations
 */
class Collector
{
    /**
     * @var array
     * [
     *  // 'base namespace' => 'the real path',
     *  'App\\Ws' => 'path'
     * ]
     */
    public $scanDirs = [];

    /**
     * @var array
     */
    private $scanClasses = [];

    /**
     * @var callable[] [name => callable]
     */
    public $handlers = [];

    /**
     * @var FileFinder
     */
    private $finder;

    /**
     * @var array
     */
    private $missClasses = [];

    /**
     * @var array
     */
    private $foundedClasses = [];


    public function __construct(FileFinder $finder = null, array $scanDirs = [])
    {
        $this->finder = $finder;
        $this->addScans($scanDirs);
    }

    /**
     * @param string $namespace
     * @param string $path
     * @return $this
     */
    public function addScan(string $namespace, string $path)
    {
        $length = strlen($namespace);

        if ('\\' !== $namespace[$length - 1]) {
            throw new \InvalidArgumentException('A non-empty PSR-4 prefix must end with a namespace separator.');
        }

        $this->scanDirs[$namespace] = $path;

        return $this;
    }

    /**
     * @param array $config
     * @return $this
     */
    public function addScans(array $config)
    {
        foreach ($config as $namespace => $path) {
            $this->addScan($namespace, $path);
        }

        return $this;
    }

    /**
     * @param array $config
     * @return $this
     */
    public function configFinder(array $config)
    {
        $this->finder = new FileFinder($config);

        return $this;
    }

    /**
     * @return $this
     */
    public function scan()
    {
        new FileFinder([
            'sourcePath' => '/var/xxx/vendor/bower/jquery'
        ]);

        return $this;
    }

    /**
     * handle resource
     * @return $this
     */
    public function handle()
    {
        foreach ($this->findFiles() as $class) {
            // class_exists 不再为已定义的 interface 返回 TRUE。请使用 interface_exists()
            if (!class_exists($class)) {
                if (interface_exists($class)) {
                    continue;
                }

                $this->missClasses[] = $class;
                continue;
            }

            $refClass = new \ReflectionClass($class);

            if (!$refClass->isInstantiable()) {
                continue;
            }

            $this->foundedClasses[] = $class;

            foreach ($this->handlers as $handler) {
                $handler($refClass);
            }
        }

        return $this;
    }

    /**
     * @param string $class
     * @return $this
     */
    public function addScanClass(string $class)
    {
        $this->scanClasses[] = $class;

        return $this;
    }

    /**
     * @return array|\Generator
     */
    public function findFiles()
    {
        foreach ($this->scanClasses as $class) {
            yield $class;
        }

        foreach ($this->scanDirs as $namespace => $dir) {
            foreach ($this->finder->setSourcePath($dir)->find(true)->getFiles() as $file) {
                yield $namespace . str_replace('/', '\\', substr($file,0, -4));
//                yield [$namespace, substr($file,0, -4)];
            }
        }

        return [];
    }

    public function getAnnotations()
    {

    }

    /**
     * @param string $name
     * @param callable $handler
     */
    public function registerHandler(string $name, callable $handler)
    {
        $this->handlers[$name] = $handler;
    }

    /**
     * @param array $handlers
     */
    public function registerHandlers(array $handlers)
    {
        foreach ($handlers as $name => $handler) {
            $this->handlers[$name] = $handler;
        }
    }

    /**
     * @return \callable[]
     */
    public function getHandlers(): array
    {
        return $this->handlers;
    }

    /**
     * @param \callable[] $handlers
     */
    public function setHandlers(array $handlers)
    {
        $this->handlers = $handlers;
    }

    /**
     * @return array
     */
    public function getFoundedClasses(): array
    {
        return $this->foundedClasses;
    }

    /**
     * @return array
     */
    public function getScanDirs(): array
    {
        return $this->scanDirs;
    }

    /**
     * @param array $scanDirs
     * @return $this
     */
    public function setScanDirs(array $scanDirs)
    {
        $this->scanDirs = $scanDirs;

        return $this;
    }

    /**
     * @return FileFinder
     */
    public function getFinder(): FileFinder
    {
        return $this->finder;
    }

    /**
     * @param FileFinder $finder
     * @return $this
     */
    public function setFinder(FileFinder $finder)
    {
        $this->finder = $finder;

        return $this;
    }
}