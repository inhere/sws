<?php
/**
 * Created by PhpStorm.
 * User: Inhere
 * Date: 2017/3/28 0028
 * Time: 23:00
 *
 * @from slim3
 */

namespace Sws\Http;

use inhere\library\collections\SimpleCollection;

/**
 * Class Cookies
 * @package Sws\Http
 */
class Cookies extends SimpleCollection
{
    /**
     * Cookies
     * @var array
     * [
     *  'name' => [ options ...]
     * ]
     */
    // protected $data = [];

    /**
     * Default cookie properties
     * @var array
     */
    protected $defaults = [
        'value' => '',
        'domain' => null,
        'hostOnly' => null,
        'path' => null,
        'expires' => null,
        'secure' => false,
        'httpOnly' => false
    ];

    /**
     * Set default cookie properties
     * @param array $settings
     */
    public function setDefaults(array $settings)
    {
        $this->defaults = array_replace($this->defaults, $settings);
    }

    /**
     * Set cookie
     * @param string $name Cookie name
     * @param string|array $value Cookie value, or cookie properties
     * @return $this
     */
    public function set($name, $value)
    {
        if (!is_array($value)) {
            $value = ['value' => (string)$value];
        }

        parent::set($name, array_replace($this->defaults, $value));

        return $this;
    }

    /**
     * Convert to `Set-Cookie` headers
     * @return string[]
     */
    public function toHeaders()
    {
        $headers = [];
        foreach ($this->data as $name => $properties) {
            $headers[] = $this->toHeader($name, $properties);
        }

        return $headers;
    }

    /**
     * Convert to `Set-Cookie` header
     * @param  string $name Cookie name
     * @param  array $properties Cookie properties
     * @return string
     */
    protected function toHeader($name, array $properties)
    {
        $result = urlencode($name) . '=' . urlencode($properties['value']);

        if (isset($properties['domain'])) {
            $result .= '; domain=' . $properties['domain'];
        }

        if (isset($properties['path'])) {
            $result .= '; path=' . $properties['path'];
        }

        if (isset($properties['expires'])) {
            if (is_string($properties['expires'])) {
                $timestamp = strtotime($properties['expires']);
            } else {
                $timestamp = (int)$properties['expires'];
            }
            if ($timestamp !== 0) {
                $result .= '; expires=' . gmdate('D, d-M-Y H:i:s e', $timestamp);
            }
        }

        if (isset($properties['secure']) && $properties['secure']) {
            $result .= '; secure';
        }

        if (isset($properties['hostOnly']) && $properties['hostOnly']) {
            $result .= '; HostOnly';
        }

        if (isset($properties['httpOnly']) && $properties['httpOnly']) {
            $result .= '; HttpOnly';
        }

        return $result;
    }

    /**
     * @return string
     * header: `"Cookie: $cookieValue" . Header::EOL`
     */
    public function toRequestHeader()
    {
        $cookieValue = '';

        foreach ($this->data as $name => $value) {
            $cookieValue .= urlencode($name) . '=' . urlencode($value['value']) . '; ';
        }

        return trim($cookieValue, '; ');
    }

    /**
     * Parse HTTP request `Cookie:` header and extract
     * into a PHP associative array.
     * @param  string $cookieData The raw HTTP request `Cookie:` header
     * @return array Associative array of cookie names and values
     * @throws \InvalidArgumentException if the cookie data cannot be parsed
     */
    public static function parseFromRawHeader($cookieData)
    {
        if (is_string($cookieData) === false) {
            throw new \InvalidArgumentException('Cannot parse Cookie data. Header value must be a string.');
        }

        $cookieData = rtrim($cookieData, "\r\n");
        $pieces = preg_split('@[;]\s*@', $cookieData);
        $cookies = [];

        foreach ($pieces as $cookie) {
            $cookie = explode('=', $cookie, 2);

            if (count($cookie) === 2) {
                $key = urldecode($cookie[0]);
                $value = urldecode($cookie[1]);

                if (!isset($cookies[$key])) {
                    $cookies[$key] = $value;
                }
            }
        }

        return $cookies;
    }
}