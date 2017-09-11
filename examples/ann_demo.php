<?php
/**
 * Created by PhpStorm.
 * User: inhere
 * Date: 2017-09-11
 * Time: 9:29
 */

use Sws\Annotations\Route;
use Sws\Annotations\RoutePrefix;
use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\AnnotationRegistry;

// AnnotationRegistry::registerFile("/path/to/doctrine/lib/Doctrine/ORM/Mapping/Driver/DoctrineAnnotations.php");

require dirname(__DIR__) . '/vendor/autoload.php';

// var_dump(new Route([]));
// AnnotationRegistry::registerAutoloadNamespace("Sws\Annotations", dirname(__DIR__) . '/lib/sws/Annotations');
AnnotationRegistry::registerLoader('class_exists');

/**
 * Class DocBlockExample
 *
 * @RoutePrefix("/test") - the route prefix path
 * @package test
 */
class AnnExample
{
    /**
     * @Route("index", method="GET", enter="onEnter", leave="onLeave")
     */
    public function indexAction()
    {
        // something ... ...
    }

    /**
     * @Route("test/{name}", method="GET", enter="onEnter", leave="onLeave")
     */
    public function testAction()
    {
        // something ... ...
    }
}

$annotationReader = new AnnotationReader();
//Get class annotation
$reflectionClass = new ReflectionClass('AnnExample');
$classAnnotations = $annotationReader->getClassAnnotations($reflectionClass);
echo '========= CLASS ANNOTATIONS =========' . PHP_EOL;
print_r($classAnnotations);

$refM = $reflectionClass->getMethod('testAction');
$mAnnotations = $annotationReader->getMethodAnnotations($refM);
echo '========= Method ANNOTATIONS =========' . PHP_EOL;
print_r($mAnnotations);


$annotationDemoObject = new AnnExample();
$reflectionObject = new ReflectionObject($annotationDemoObject);
$objectAnnotations = $annotationReader->getClassAnnotations($reflectionObject);
echo '========= OBJECT ANNOTATIONS =========' . PHP_EOL;
print_r($objectAnnotations);

// $reflectionProperty = new ReflectionProperty('AnnExample', 'property');
// $propertyAnnotations = $annotationReader->getPropertyAnnotations($reflectionProperty);
// echo '=========   PROPERTY ANNOTATIONS =========' . PHP_EOL;
// print_r($propertyAnnotations);
