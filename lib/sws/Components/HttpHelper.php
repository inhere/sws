<?php
/**
 * Created by PhpStorm.
 * User: inhere
 * Date: 2017-08-29
 * Time: 15:25
 */

namespace Sws\Components;

use Inhere\Http\ServerRequest;
use Inhere\Http\Response;
use Inhere\Http\UploadedFile;
use Inhere\Http\Uri;

use Swoole\Http\Response as SwResponse;
use Swoole\Http\Request as SwRequest;

/**
 * Class HttpHelper
 * @package Sws\Components
 */
class HttpHelper
{
    /**
     * @param SwRequest $swRequest
     * @return ServerRequest
     */
    public static function createRequest(SwRequest $swRequest)
    {
        $uri = $swRequest->server['request_uri'];
        $method = $swRequest->server['request_method'];
        $request = new ServerRequest($method, Uri::createFromString($uri));

        // add attribute data
        $request->setAttribute('_fd', $swRequest->fd);

        // GET data
        if (isset($swRequest->get)) {
            $request->setParsedBody($swRequest->get);
        }

        // POST data
        if (isset($swRequest->post)) {
            $request->setParsedBody($swRequest->post);
        }

        // cookie data
        if (isset($swRequest->cookie)) {
            $request->setCookies($swRequest->cookie);
        }

        // FILES data
        if (isset($swRequest->files)) {
            $request->setUploadedFiles(UploadedFile::parseUploadedFiles($swRequest->files));
        }

        // SERVER data
        $serverData = array_change_key_case($swRequest->server, CASE_UPPER);

        if ($swRequest->header) {
            // headers
            $request->setHeaders($swRequest->header);

            // 将 HTTP 头信息赋值给 $serverData
            foreach ((array)$swRequest->header as $key => $value) {
                $_key = 'HTTP_' . strtoupper(str_replace('-', '_', $key));
                $serverData[$_key] = $value;
            }
        }

        $request->setServerParams($serverData);

        return $request;
    }

    /**
     * @return Response
     */
    public static function createResponse()
    {
        $headers = ['Content-Type' => 'text/html; charset=' . \Sws::get('config')->get('charset', 'UTF-8')];

        return new Response(200, $headers);
    }

    /**
     * @param Response $response
     * @param SwResponse $swResponse
     * @return SwResponse
     */
    public static function sendResponse(Response $response, SwResponse $swResponse)
    {
        // set http status
        $swResponse->status($response->getStatus());

        // set headers
        foreach ($response->getHeadersObject()->getLines() as $name => $value) {
            $swResponse->header($name, $value);
        }

        // set cookies
        foreach ($response->getCookies()->toHeaders() as $value) {
            $swResponse->header('Set-Cookie', $value);
        }

        // write content
        if ($body = (string)$response->getBody()) {
            $swResponse->write($body);
        }

        // send response to client
        return $swResponse->end();
    }
}
