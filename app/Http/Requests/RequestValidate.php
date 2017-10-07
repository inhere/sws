<?php
/**
 * Created by PhpStorm.
 * User: inhere
 * Date: 2017-09-30
 * Time: 9:46
 */

namespace App\Http\Requests;

use Inhere\Library\Collections\SimpleCollection;
use Inhere\Validate\ValidationTrait;

/**
 * Class Request
 * @package App\Http\Requests
 */
class RequestValidate extends SimpleCollection
{
    use ValidationTrait;

    /**
     * Create new collection
     * @param array $items Pre-populate collection with this key-value array
     */
    public function __construct(array $items = [])
    {
        parent::__construct($items);

        $this->init();
    }

    protected function init()
    {
    }

    /**
     * {@inheritDoc}
     */
    public function rules()
    {
        return [
            ['name', 'string', 'on' => 'index'],
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function translates()
    {
        return [
            // 'field' => 'translate',
            // e.g. 'name'=>'名称',
        ];
    }
}
