<?php
/**
 * Created by PhpStorm.
 * User: inhere
 * Date: 2017-08-17
 * Time: 11:30
 */

namespace Sws\Components;

use Inhere\Http\ServerRequest as Request;

/**
 * Class OriginCheck
 * @package Sws\Components
 */
class OriginCheck
{
    /**
     * @var array
     */
    private $allowedOrigins = [];

    /**
     * OriginCheck constructor.
     * @param array $allowed
     */
    public function __construct(array $allowed = [])
    {
        $this->allowedOrigins = array_merge($this->allowedOrigins, $allowed);
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function check($request)
    {
        $header = $request->getHeaderLine('Origin');
        $origin = parse_url($header, PHP_URL_HOST) ?: $header;

        return \in_array($origin, $this->allowedOrigins, true);
    }

    /**
     * @return array
     */
    public function getAllowedOrigins(): array
    {
        return $this->allowedOrigins;
    }
}
