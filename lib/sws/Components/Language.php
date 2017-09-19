<?php
/**
 * Created by PhpStorm.
 * User: inhere
 * Date: 2017/9/19
 * Time: 下午11:24
 */

namespace Sws\Components;

use inhere\library\helpers\Arr;
use inhere\library\helpers\Str;
use inhere\library\files\File;

/**
 * Class Language
 * @package Sws\Components
 */
class Language
{
    /**
     * support formats
     * @var array
     */
    private static $formats = ['json', 'php', 'ini', 'yml'];

    /**
     * current use language
     * @var string
     */
    private $lang = 'en';

    /**
     * fallback language
     * @var string
     */
    private $fallbackLang = 'en';

    /**
     * @var array[]
     * [ 'filename' => array ]
     */
    private $data = [];

    /**
     * The base path language directory.
     * @var string
     */
    private $basePath;

    /**
     * default file name.
     * @var string
     */
    private $defaultFile = 'default';

    /**
     * the language file type. more see File::FORMAT_*
     * @var string
     */
    private $fileType = File::FORMAT_PHP;

    /**
     * level separator char.
     * e.g:
     *  $language->tran('app.createPage');
     * @var string
     */
    private $separator = '.';

    /**
     * e.g.
     * [
     *    'user'  => '/xx/yy/zh-cn/user.yml'
     *    'admin' => '/xx/yy/zh-cn/admin.yml'
     * ]
     * @var array
     */
    private $langFiles = [];

    /**
     * loaded language file list.
     * @var array
     */
    private $loadedFiles = [];

    /**
     * whether ignore not exists lang file when addLangFile()
     * @var bool
     */
    private $ignoreError = false;

    const DEFAULT_FILE_KEY = '__default';

    /**
     * Language constructor.
     * @param array $config
     */
    public function __construct(array $config = [])
    {

    }

    /**
     * {@inheritdoc}
     * @see self::translate()
     */
    public function trans($key, array $args = [], $default = '')
    {
        return $this->translate($key, $args, $default);
    }

    /**
     * {@inheritdoc}
     * @see self::translate()
     */
    public function tl($key, array $args = [], $default = '')
    {
        return $this->translate($key, $args, $default);
    }

    /**
     *
     * @param string|bool $key 'site-name' or 'user.login'
     * @param array $args
     * @param string $default
     * @param null|string $lang
     * @return array|string
     */
    public function translate(string $key, array $args = [], $default = '', $lang = null)
    {
        if (!$key || !is_string($key)) {
            throw new \InvalidArgumentException('A lack of parameters or type error.');
        }

        $lang = $lang ?: $this->lang;

        if (!$langData = $this->getLangData($lang, false)) {
            throw new \InvalidArgumentException('No language data of the lang: ' . $lang);
        }

        $value = Arr::getByPath($langData, $key, $default, $this->separator);

        // no translate text
        if ($value === '' || $value === null) {
            return ucfirst(Str::toSnakeCase(str_replace(['-', '_', '.'], ' ', $key), ' '));
        }

        // $args is not empty
        if ($args) {
            array_unshift($args, $value);

            return sprintf(...$args);
        }

        return $value;
    }

    /**
     * @param string $lang
     * @param bool $toIterator
     * @return array|\ArrayIterator|null
     */
    public function getLangData(string $lang, $toIterator = true)
    {
        if (isset($this->data[$lang])) {
            return $toIterator ? new \ArrayIterator($this->data[$lang]) : $this->data[$lang];
        }

        return null;
    }

    /**
     * @param string $key
     * @param null|string $lang
     * @return bool
     */
    public function has(string $key, $lang = null)
    {
        $lang = $lang ?: $this->lang;

        if (!$langData = $this->getLangData($lang, false)) {
            return false;
        }

        return Arr::getByPath($langData, $key, null, $this->separator) !== null;
    }

    /*********************************************************************************
     * getter/setter
     *********************************************************************************/

    /**
     * Allow quick access default file translate by `$lang->key`,
     * is equals to `$lang->tl('key')`.
     * @param string $name
     * @return mixed|string
     * @throws \InvalidArgumentException
     */
    public function __get($name)
    {
        return $this->translate($name);
    }

    /**
     * @param $key
     * @param $name
     */
    public function __set($key, $name)
    {
        //
    }

    public function __isset($key)
    {
        return $this->has($key);
    }

    /**
     * Allow quick access default file translate by `$lang->key()`,
     * is equals to `$lang->tl('key')`.
     * @param string $name
     * @param array $args
     * @return mixed|string
     * @throws \InvalidArgumentException
     */
    public function __call($name, $args)
    {
        return $this->translate($name);
    }

    /**
     * @return string
     */
    public function getLang()
    {
        return $this->lang;
    }

    /**
     * @param string $lang
     */
    public function setLang($lang)
    {
        $this->lang = trim($lang);
    }

    /**
     * @return string
     */
    public function getBasePath()
    {
        return $this->basePath;
    }

    /**
     * @param string|array $path
     */
    public function setBasePath($path)
    {
        if ($path && is_dir($path)) {
            $this->basePath = $path;
        }
    }

    /**
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @return array
     */
    public function getLangFiles()
    {
        return $this->langFiles;
    }

    /**
     * @param array $langFiles
     * @throws \inhere\exceptions\InvalidArgumentException
     * @throws \inhere\exceptions\NotFoundException
     */
    public function setLangFiles(array $langFiles)
    {
        foreach ($langFiles as $fileKey => $file) {
            $this->addLangFile($file, is_numeric($fileKey) ? '' : $fileKey);
        }
    }

    /**
     * @param bool $full
     * @return string
     */
    public function getDefaultFile($full = false)
    {
        return $full ? $this->getLangFile(self::DEFAULT_FILE_KEY) : $this->defaultFile;
    }

    /**
     * @return string
     */
    public function getFallbackLang()
    {
        return $this->fallbackLang;
    }

    /**
     * @param string $fallbackLang
     */
    public function setFallbackLang($fallbackLang)
    {
        $this->fallbackLang = $fallbackLang;
    }

    /**
     * @return string
     */
    public function getFileType()
    {
        return $this->fileType;
    }

    /**
     * @param string $fileType
     */
    public function setFileType($fileType)
    {
        if (in_array($fileType, self::$formats, true)) {
            $this->fileType = $fileType;
        }
    }

    /**
     * @return string
     */
    public function getSeparator()
    {
        return $this->separator;
    }

    /**
     * @param string $separator
     */
    public function setSeparator($separator)
    {
        $this->separator = $separator;
    }

    /**
     * @return array
     */
    public function getLoadedFiles()
    {
        return $this->loadedFiles;
    }

    /**
     * @return bool
     */
    public function isIgnoreError()
    {
        return $this->ignoreError;
    }

    /**
     * @param bool $ignoreError
     */
    public function setIgnoreError($ignoreError)
    {
        $this->ignoreError = (bool)$ignoreError;
    }

    /**
     * @return array
     */
    public static function getFormats(): array
    {
        return self::$formats;
    }
}
