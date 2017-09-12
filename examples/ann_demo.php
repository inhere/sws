<?php
/**
 * Created by PhpStorm.
 * User: inhere
 * Date: 2017-09-11
 * Time: 9:29
 */

use inhere\library\collections\SimpleCollection;
use inhere\library\files\FileFinder;
use Sws\Annotations\Service;
use Sws\Annotations\Controller;
use Sws\Annotations\DI;
use Sws\Annotations\Inject;
use Sws\Annotations\Route;
use Sws\Annotations\RpcService;
use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\AnnotationRegistry;

require dirname(__DIR__) . '/vendor/autoload.php';

// AnnotationRegistry::registerFile("/path/to/doctrine/lib/Doctrine/ORM/Mapping/Driver/DoctrineAnnotations.php");
// AnnotationRegistry::registerAutoloadNamespace("Sws\Annotations", dirname(__DIR__) . '/lib/sws/Annotations');
AnnotationRegistry::registerLoader('class_exists');
AnnotationReader::addGlobalIgnoredName('foo');

/**
 * Class DocBlockExample
 *
 * @Service("db", shared="1", alias={"database","myDb"})
 * @RpcService("ann")
 * @Controller("/test", type=Controller::REST)
 * @package test
 */
class AnnExample
{
    /**
     * @Inject("user")
     * @var SimpleCollection
     */
    public $prop;

    /**
     * @DI("test")
     * @Route("index", method="GET", enter="onEnter", leave="onLeave")
     */
    public function indexAction()
    {
        // something ... ...
    }

    /**
     * @Route("test/{name}", method={"GET", "POST"}, enter="onEnter", leave="onLeave", tokens={"name"="\w+"})
     */
    public function testAction()
    {
        // something ... ...
    }
}

$ff = new FileFinder([
    'sourcePath' => dirname(__DIR__) . '/app/',
    'include' => [
        'ext' => ['php']
    ]
]);
$ff->setFileFilter(function ($name) {
    // 必须首字符大写(类文件)
    return preg_match('/[A-Z]/', $name);
});

var_dump($ff->findAll(1)->getFiles());die;

$annotationReader = new AnnotationReader();
//Get class annotation
$reflectionClass = new ReflectionClass('AnnExample');
$classAnnotations = $annotationReader->getClassAnnotations($reflectionClass);
echo '========= CLASS ANNOTATIONS =========' . PHP_EOL;
print_r($classAnnotations);

$refMethod = $reflectionClass->getMethod('testAction');
$mAnnotations = $annotationReader->getMethodAnnotations($refMethod);
echo '========= Method ANNOTATIONS =========' . PHP_EOL;
print_r($mAnnotations);

$refProp = $reflectionClass->getProperty('prop');
$mAnnotations = $annotationReader->getPropertyAnnotations($refProp);
echo '========= Property ANNOTATIONS =========' . PHP_EOL;
print_r($mAnnotations);

//
//$annotationDemoObject = new AnnExample();
//$reflectionObject = new ReflectionObject($annotationDemoObject);
//$objectAnnotations = $annotationReader->getClassAnnotations($reflectionObject);
//echo '========= OBJECT ANNOTATIONS =========' . PHP_EOL;
//print_r($objectAnnotations);

// $reflectionProperty = new ReflectionProperty('AnnExample', 'property');
// $propertyAnnotations = $annotationReader->getPropertyAnnotations($reflectionProperty);
// echo '=========   PROPERTY ANNOTATIONS =========' . PHP_EOL;
// print_r($propertyAnnotations);
