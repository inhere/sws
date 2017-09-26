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