<?php
/**
 * Created by PhpStorm.
 * User: inhere
 * Date: 2017-09-12
 * Time: 17:30
 */

namespace Sws\Annotations;

use Doctrine\Common\Annotations\Reader;
use Doctrine\Common\Annotations\IndexedReader;
use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\AnnotationRegistry;
use inhere\library\files\FileFinder;
use ReflectionClass;
use ReflectionMethod;
use Sws\Annotations\Handlers\HandlerInterface;

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

    /** @var FileFinder */
    private $finder;

    /** @var Reader  */
    private $reader;

    /** @var array  */
    private $missClasses = [];

    /** @var array  */
    private $handledClasses = [];

    /** @var int  */
    private $foundedCount = 0;

    /**
     * Collector constructor.
     * @param FileFinder|null $finder
     * @param array $scanDirs
     */
    public function __construct(FileFinder $finder = null, array $scanDirs = [])
    {
        $this->finder = $finder;

        $this->reader = new IndexedReader(new AnnotationReader());

        $this->addScans($scanDirs);

        $this->init();
    }

    protected function init()
    {
        AnnotationRegistry::registerLoader('class_exists');
        AnnotationReader::addGlobalIgnoredName('from');
        AnnotationReader::addGlobalIgnoredName('notice');
        AnnotationReader::addGlobalIgnoredName('Notice');
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
     * @param array $classes
     * @return $this
     */
    public function addScanClasses(array $classes)
    {
        $this->scanClasses = array_merge($this->scanClasses, $classes);

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
     * @var array
     * [
     *  class name => [
     *  'class' => class annotations,
     *  'prop' => props annotations,
     *  'method' => methods annotations,
     * ]
     * ... ...
     * ]
     */
    private $annotations = [];

    /**
     * handle resource
     * @return $this
     */
    public function handle()
    {
        $timer = 0;

        foreach ($this->findFiles() as $class) {
            $timer++;

            // class_exists 不再为已定义的 interface 返回 TRUE。请使用 interface_exists()
            if (!class_exists($class)) {
                if (interface_exists($class)) {
                    continue;
                }

                $this->missClasses[] = $class;
                continue;
            }

            // deny repeat class
            if (isset($this->annotations[$class])) {
                continue;
            }

            $refClass = new ReflectionClass($class);

            if (!$refClass->isInstantiable()) {
                continue;
            }

            $hash = spl_object_hash($refClass);
            $this->handledClasses[$hash] = $class;

            $classAnn = $this->reader->getClassAnnotations($refClass);
            $this->addAnnotations($class, $classAnn);

            $this->getAllPropsAnnotations($refClass);
            $this->getAllMethodsAnnotations($refClass);

            foreach ($this->handlers as $handler) {
                $handler($this, $classAnn, $refClass);
            }
        }

        $this->foundedCount = $timer;

        return $this;
    }

    /**
     * @param ReflectionClass $refClass
     */
    public function getAllPropsAnnotations(ReflectionClass $refClass)
    {
        $class = $refClass->getName();
        $props = $refClass->getProperties();

        foreach ($props as $refProp) {
            $pAnnotations = $this->reader->getPropertyAnnotations($refProp);

            if ($pAnnotations) {
                $this->addAnnotations($class, $pAnnotations, Position::AT_PROPERTY, $refProp->getName());
            }
        }
    }

    /**
     * @param ReflectionClass $refClass
     */
    public function getAllMethodsAnnotations(ReflectionClass $refClass)
    {
        $class = $refClass->getName();

        foreach ($refClass->getMethods(ReflectionMethod::IS_PUBLIC) as $refMethod) {

            if ($mAnnotations = $this->reader->getMethodAnnotations($refMethod)) {
                $this->addAnnotations($class, $mAnnotations, Position::AT_METHOD, $refMethod->getName());
            }
        }
    }

    /**
     * @return \Generator
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

        return null;
    }

    /**
     * @param $class
     * @param $annotations
     * @param string $type Allow class, method, prop
     * @param null|string $name when type is method or prop, this is method name or prop name
     */
    public function addAnnotations($class, array $annotations, $type = Position::AT_CLASS, $name = null)
    {
        if (!isset($this->annotations[$class])) {
            $this->annotations[$class] = [];
        }

        if ($type === Position::AT_CLASS) {
            $this->annotations[$class][$type] = $annotations;

        } elseif (($type === Position::AT_PROPERTY || $type === Position::AT_METHOD) && $name) {
            $this->annotations[$class][$type][$name] = $annotations;
        }
    }

    /**
     * @param null $class
     * @return array
     */
    public function getAnnotations($class = null)
    {
        if ($class) {
            return $this->annotations[$class] ?? null;
        }

        return $this->annotations;
    }

    /**
     * @param string $class
     * @param string $type
     * @param string|null $name
     * @return array|null
     */
    public function getAnnotationsByType($class, $type = Position::AT_CLASS, $name = null)
    {
        $annotations = $this->annotations[$class][$type] ?? null;

        if (($type === Position::AT_PROPERTY || $type === Position::AT_METHOD) && $name && $annotations) {
            return $annotations[$name] ?? null;
        }

        return $annotations;
    }

    /**
     * @param string $name
     * @param callable $handler
     */
    public function registerHandler(string $name, callable $handler)
    {
        if ($handler instanceof HandlerInterface) {
            $handler->setCollector($this);
        }

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
    public function getHandledClasses(): array
    {
        return $this->handledClasses;
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

    /**
     * @return Reader
     */
    public function getReader(): Reader
    {
        return $this->reader;
    }
}
