<?php
/**
 * Created by PhpStorm.
 * User: inhere
 * Date: 2017-03-30
 * Time: 19:02
 */

namespace sws\http;

use inhere\library\collections\SimpleCollection;

/**
 * Class Headers
 * @package sws\http
 */
class Headers extends SimpleCollection
{
    /**
     * the connection header line data end char
     */
    const EOL = "\r\n";

    const HEADER_END = "\r\n\r\n";

    /**
     * Special HTTP headers that do not have the "HTTP_" prefix
     *
     * @var array
     */
    protected static $special = [
        'CONTENT_TYPE' => 1,
        'CONTENT_LENGTH' => 1,
        'PHP_AUTH_USER' => 1,
        'PHP_AUTH_PW' => 1,
        'PHP_AUTH_DIGEST' => 1,
        'AUTH_TYPE' => 1,
    ];


    /**
     * Return array of HTTP header names and values.
     * This method returns the _original_ header name
     * as specified by the end user.
     *
     * @return array
     */
    public function all()
    {
        $all = parent::all();
        $out = [];
        foreach ($all as $key => $props) {
            $out[$props['originalKey']] = $props['value'];
        }

        return $out;
    }

    /**
     * Set HTTP header value
     * This method sets a header value. It replaces
     * any values that may already exist for the header name.
     * @param string $key The case-insensitive header name
     * @param string|array $value The header value
     * @return mixed
     */
    public function set($key, $value)
    {
        if (!is_array($value)) {
            $value = [$value];
        }

        return parent::set($this->normalizeKey($key), [
            'value' => $value,
            'originalKey' => $key
        ]);
    }

    /**
     * Get HTTP header value
     *
     * @param  string  $key     The case-insensitive header name
     * @param  mixed   $default The default value if key does not exist
     *
     * @return string[]
     */
    public function get(string $key, $default = null)
    {
        if ($this->has($key)) {
            return parent::get($this->normalizeKey($key))['value'];
        }

        return $default;
    }

    /**
     * @param $name
     * @param null $default
     * @return null|string
     */
    public function getLine($name, $default = null)
    {
        if ($val = $this->get($name)) {
            return implode(',', $val);
        }

        return $default;
    }

    /**
     * @inheritdoc
     */
    public function add($key, $value)
    {
        if (!$value) {
            return $this;
        }

        return parent::add($this->normalizeKey($key), $value);
    }

    /**
     * @inheritdoc
     */
    public function has(string $key)
    {
        return parent::has($this->normalizeKey($key));
    }

    /**
     * @inheritdoc
     */
    public function remove($key)
    {
        parent::remove($this->normalizeKey($key));
    }

    /**
     * @param $key
     * @return bool|string
     */
    public function normalizeKey($key)
    {
        $key = str_replace('_', '-', strtolower($key));

        if (strpos($key, 'http-') === 0) {
            $key = substr($key, 5);
        }

        return $key;
    }

    /**
     * get client supported languages from header
     * eg: `Accept-Language:zh-CN,zh;q=0.8`
     * @return array
     */
    public function getAcceptLanguages()
    {
        $ls = [];

        if ($value = $this->getLine('Accept-Language')) {
            if (strpos($value, ';')) {
                list($value,) = explode(';', $value, 2);
            }

            $value = str_replace(' ', '', $value);
            $ls = explode(',', $value);
        }

        return $ls;
    }

    /**
     * get client supported languages from header
     * eg: `Accept-Encoding:gzip, deflate, sdch, br`
     * @return array
     */
    public function getAcceptEncodes()
    {
        $ens = [];

        if ($value = $this->getLine('Accept-Encoding')) {
            if (strpos($value, ';')) {
                list($value,) = explode(';', $value, 2);
            }

            $value = str_replace(' ', '', $value);
            $ens = explode(',', $value);
        }

        return $ens;
    }

    /**
     * @param bool $toString
     * @return array
     */
    public function toHeaderLines($toString = false)
    {
        $output = [];

        foreach ($this->data as $name => $info) {
            $name = ucwords($name, '-');
            $value = implode(',', $info['value']);
            $output[] = "$name: $value\r\n";
        }

        return $toString ? implode('', $output) : $output;
    }

    /**
     * @return array
     */
    public function getLines()
    {
        $output = [];

        foreach ($this->data as $name => $info) {
            $name = ucwords($name, '-');
            $output[$name] = implode(',', $info['value']);
        }

        return $output;
    }
}
