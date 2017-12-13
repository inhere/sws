<?php
/**
 * Created by PhpStorm.
 * User: inhere
 * Date: 2017/9/25
 * Time: 下午11:47
 */

namespace Sws\Annotations\Handlers;

use Inhere\Library\Helpers\Obj;
use PhpDocReader\PhpDocReader;
use Sws\Annotations\Collector;

/**
 * Class AbstractHandler
 * @package Sws\Annotations\Handlers
 */
abstract class AbstractHandler implements HandlerInterface
{
    /**
     * @var Collector
     */
    protected $collector;

    /**
     * @var PhpDocReader
     */
    protected $docReader;

    /**
     * @param Collector $collector
     */
    public function setCollector(Collector $collector)
    {
        $this->collector = $collector;
    }

    /**
     * @return PhpDocReader
     */
    public function getDocReader(): PhpDocReader
    {
        if (!$this->docReader) {
            $this->docReader = new PhpDocReader();
        }

        return $this->docReader;
    }

    /**
     * @param \ReflectionProperty $property
     * @return mixed
     * @throws \PhpDocReader\AnnotationException
     */
    protected function parsePropertyInject(\ReflectionProperty $property)
    {
        $property->setAccessible(true);

        $reader = $this->getDocReader();

        // Read a property type (@var phpdoc)
        // $property = new ReflectionProperty($className, $propertyName);
        $propertyClass = $reader->getPropertyClass($property);

        if ($propertyClass) {
//            return new $propertyClass;
            return new $propertyClass;
        }

        return null;
    }

    /**
     * @param \ReflectionParameter $parameter
     * @return mixed|null
     * @throws \PhpDocReader\AnnotationException
     */
    protected function parseParameterInject(\ReflectionParameter $parameter)
    {
        // Read a parameter type (@param phpdoc)
//        $parameter = new \ReflectionParameter(array($className, $methodName), $parameterName);

        $reader = $this->getDocReader();

        $parameterClass = $reader->getParameterClass($parameter);

        if ($parameterClass) {
            return Obj::get($parameterClass);
        }

        return null;
    }
}
