<?php
/**
 * Created by PhpStorm.
 * User: inhere
 * Date: 2017-09-29
 * Time: 15:46
 */

namespace Sws\Web;

use App\Http\Requests\RequestValidate;
use Inhere\Http\Request;
use Inhere\Http\Response;
use Sws\Context\ContextGetTrait;

/**
 * Class BaseController
 * @package Sws\Web
 */
abstract class BaseController
{
    use ContextGetTrait;

    /**
     * @var int
     */
    private $type = 1;

    /**
     * @var array
     * [ action name => method name]
     */
    private $actions = [];

    /**
     * @var array
     * [
     *  action => RequestValidate
     * ]
     */
    private $validations = [];

    /**
     * @param Request $req
     * @return bool
     */
    public function isAjax(Request $req = null)
    {
        $req = $req ?: $this->getRequest();

        return $req->isXhr();
    }

    /**
     * @param string $url
     * @param int $status
     * @param Response $response
     * @return mixed
     */
    public function redirect($url, $status = 302, $response = null)
    {
        $response = $response ?: $this->getResponse();

        return $response->redirect($url, $status);
    }

    /**
     * @return array
     */
    public function getActions(): array
    {
        return $this->actions;
    }

    /**
     * @param array $actions
     */
    public function setActions(array $actions)
    {
        $this->actions = $actions;
    }

    /**
     * @return array
     */
    public function getValidations(): array
    {
        return $this->validations;
    }

    /**
     * @param array $validations
     */
    public function setValidations(array $validations)
    {
        $this->validations = $validations;
    }

    /**
     * @param string $name
     * @param RequestValidate $validation
     */
    public function setValidation(string $name, RequestValidate $validation)
    {
        $this->validations[$name] = $validation;
    }

    /**
     * @return int
     */
    public function getType(): int
    {
        return $this->type;
    }

    /**
     * @param int $type
     */
    public function setType(int $type)
    {
        $this->type = $type;
    }
}
