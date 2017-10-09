<?php
/**
 * Created by PhpStorm.
 * User: inhere
 * Date: 2017-09-11
 * Time: 9:29
 */

use Doctrine\Common\Annotations\AnnotationRegistry;
use Doctrine\Common\Annotations\DocParser;
use Doctrine\Common\Annotations\SimpleAnnotationReader;
use Inhere\Library\Collections\SimpleCollection;
use Inhere\Library\Files\FileFinder;
use Sws\Annotations\Collector;
use Sws\Annotations\Tags\Service;
use Sws\Annotations\Tags\Controller;
use Sws\Annotations\Tags\DI;
use Sws\Annotations\Tags\Inject;
use Sws\Annotations\Tags\Route;
use Sws\Annotations\Tags\RpcService;
use Sws\Annotations\Tags\Parameter;
use Sws\Annotations\Tags\Parameters;
use Doctrine\Common\Annotations\AnnotationReader;
use Sws\Context\HttpContext;

require dirname(__DIR__) . '/vendor/autoload.php';

/**
 * Class DocBlockExample
 *
 * @Service("db", shared="1", alias={"database","myDb"})
 * @RpcService("ann")
 * @Controller(prefix="/test", type=Controller::REST)
 *
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
     * @var string
     */
    public $prop0;

    /**
     * @DI("logger")
     * @Route("index", method="GET", enter="onEnter", leave="onLeave", schemes={"http"}, )
     * @Parameters({
     *     @Parameter("name", type="string", rule="string; length:2,10;", required = true),
     *     @Parameter("age", type="int", rule="number; length:2,10;", required = true),
     *     @Parameter("sex", type="int", rule="in:0,1;", default="0")
     * })
     * @param HttpContext $ctx
     */
    public function indexAction(HttpContext $ctx)
    {
        // something ... ...
    }

    /**
     * @Route("test/{name}", method={"GET", "POST"}, enter="onEnter", leave="onLeave", tokens={"name"="\w+"})
     * @Context(
     *     @Request(type="json"),
     *     @Response(type="json"),
     * )
     *
     * @Request(
     *     dataType="json",
     *     parameters={
     *          @Parameter("name", type="string", rule="string; length:2,10;", required = true),
     *     }
     * )
     *
     * @API\Tag(name="users", description="用户接口")
     * @API\Tag(name="test", description="test接口")
     * @API\Info(
     *     tags={"users"},
     *     version="1.0.0",
     *     title="XXX API接口",
     *     description="获取用户信息"
     * )
     */
    protected function testAction()
    {
        // something ... ...
    }
}

AnnotationRegistry::registerLoader('class_exists');
//$sReader = new AnnotationReader();
$sReader = new SimpleAnnotationReader();
$parser = new DocParser();
$refClass = new ReflectionClass('AnnExample');
$refProp = $refClass->getProperty('prop');
//$cAnn = $sReader->getClassAnnotations($refClass);
$pAnn = $sReader->getPropertyAnnotations($refProp);

var_dump($parser->parse($refProp->getDocComment()), $pAnn);die;

$conf = [
//    'base namespace' => 'the real path',
    'path' => 'App',
];

$ff = new FileFinder([
    // 'sourcePath' => dirname(__DIR__) . '/app/',
    'include' => [
        'ext' => ['php']
    ],
    'exclude' => [
        'file' => 'Sws.php',
        'dir' => ['Console','Helpers', 'Annotations'], // 排除目录
    ]
]);

$ff->setFileFilter(function ($name) {
    // 必须首字符大写(类文件)
    return preg_match('/[A-Z]/', $name);
});

//$files = $ff->find(true)->getFiles();
//$f2 = $ff->setSourcePath(dirname(__DIR__) . '/lib/sws/Components')->find(true)->getFiles();
//var_dump($files, $f2);

$clt = new Collector($ff, null, [
    'App\\' => dirname(__DIR__) . '/app/',
    'Sws\\Components\\' => dirname(__DIR__) . '/lib/sws/Components',
]);

$clt->addScan('Sws\\Module\\', dirname(__DIR__) . '/lib/sws/Module');
$clt->addScanClass(AnnExample::class);

$clt->registerHandlers([
    'service' => function (Collector $clt, $classAnn, \ReflectionClass $refClass) {

//        $sReader = new SimpleAnnotationReader();
//        $mAnnotations = $sReader->getMethodAnnotations($refMethod);

        pr($clt->getAnnotations(), -4);
    },
//    'wsModule' => function (\ReflectionClass $refClass) {
//
//    },
//    'route' => function (\ReflectionClass $refClass) {
//
//    },
//    'rpcService' => function (\ReflectionClass $refClass) {
//
//    },
]);
$clt->handle();
pr($clt, -4);

AnnotationRegistry::registerLoader('class_exists');
$annotationReader = new AnnotationReader();
//Get class annotation
$refClass = new ReflectionClass('AnnExample');
echo "class Id: {$refClass->getNamespaceName()}{$refClass->getName()}\n";
$classAnnotations = $annotationReader->getClassAnnotations($refClass);
echo '========= CLASS ANNOTATIONS =========' . PHP_EOL;
pr($classAnnotations);

$refMethod = $refClass->getMethod('testAction');
$mAnnotations = $annotationReader->getMethodAnnotations($refMethod);
echo '========= Method ANNOTATIONS =========' . PHP_EOL;
pr($mAnnotations);

$refProp = $refClass->getProperty('prop');
$mAnnotations = $annotationReader->getPropertyAnnotations($refProp);
echo '========= Property ANNOTATIONS =========' . PHP_EOL;
pr($mAnnotations);

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
