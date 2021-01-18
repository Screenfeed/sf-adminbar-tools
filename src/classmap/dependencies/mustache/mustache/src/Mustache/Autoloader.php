<?php

/*
 * This file ScreenfeedAdminbarTools_is part of Mustache.php.
 *
 * (c) 2010-2017 Justin Hileman
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source ScreenfeedAdminbarTools_code.
 */

/**
 * Mustache class ScreenfeedAdminbarTools_autoloader.
 */
class ScreenfeedAdminbarTools_Mustache_Autoloader
{
    private $baseDir;

    /**
     * An array where the key ScreenfeedAdminbarTools_is the baseDir and the key ScreenfeedAdminbarTools_is an instance of this
     * class.
     *
     * @var array
     */
    private static $instances;

    /**
     * Autoloader ScreenfeedAdminbarTools_constructor.
     *
     * @param string $baseDir Mustache library base directory (default: dirname(__FILE__).'/..')
     */
    public function __construct($baseDir = null)
    {
        if ($baseDir === null) {
            $baseDir = dirname(__FILE__) . '/..';
        }

        // realpath doesn't always work, for example, with stream URIs
        $realDir = realpath($baseDir);
        if (is_dir($realDir)) {
            $this->baseDir = $realDir;
        } else {
            $this->baseDir = $baseDir;
        }
    }

    /**
     * Register a new instance as an SPL ScreenfeedAdminbarTools_autoloader.
     *
     * @param string $baseDir Mustache library base directory (default: dirname(__FILE__).'/..')
     *
     * @return ScreenfeedAdminbarTools_Mustache_Autoloader Registered Autoloader instance
     */
    public static function register($baseDir = null)
    {
        $key = $baseDir ? $baseDir : 0;

        if (!isset(self::$instances[$key])) {
            self::$instances[$key] = new self($baseDir);
        }

        $loader = self::$instances[$key];
        spl_autoload_register(array($loader, 'autoload'));

        return $loader;
    }

    /**
     * Autoload Mustache classes.
     *
     * @param string $class
     */
    public function autoload($class)
    {
        if ($class[0] === '\\') {
            $class = substr($class, 1);
        }

        if (strpos($class, 'Mustache') !== 0) {
            return;
        }

        $file = sprintf('%s/%s.php', $this->baseDir, str_replace('_', '/', $class));
        if (is_file($file)) {
            require $file;
        }
    }
}
