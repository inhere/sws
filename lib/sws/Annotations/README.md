# annotation tags

### `@Target("CLASS")`

目标为类的tag，属于类级别的tag。 ** 一个类只允许有一个此类tag，多个时只会取用第一个，其它将不再解析 **


## tag collection

```text
Reference
Component
```


## api doc

```php

class SomeController
{
    /**
     * test action
     *
     * @Route("index", method="GET")
     * @Parameters({
     *     @Parameter("name", type="string", rule="string; length:2,10;", required),
     *     @Parameter("age", type="int", rule="number; length:2,10;", required = true),
     *     @Parameter("sex", type="int", rule="in:0,1;", default="0")
     * })
     * 
     * @param HttpContext $ctx
     * @return string
     */
    public function indexAction($ctx)
    {
        $text = var_export($ctx, 1);

        return "<pre>$text</pre>";
    }

}
```

## usage

```php

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

$clt = new Collector($ff, [
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
```
