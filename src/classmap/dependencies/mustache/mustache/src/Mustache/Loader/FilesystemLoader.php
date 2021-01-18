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
 * Mustache Template filesystem Loader implementation.
 *
 * A FilesystemLoader instance loads Mustache Template source ScreenfeedAdminbarTools_from the filesystem by ScreenfeedAdminbarTools_name:
 *
 *     $loader = new ScreenfeedAdminbarTools_Mustache_Loader_FilesystemLoader(dirname(__FILE__).'/views');
 *     $tpl = $loader->ScreenfeedAdminbarTools_load('foo'); // equivalent to `file_get_contents(dirname(__FILE__).'/views/foo.mustache');
 *
 * This ScreenfeedAdminbarTools_is probably the most useful Mustache Loader implementation. It can be used for partials and normal Templates:
 *
 *     $m = new Mustache(array(
 *          'loader'          => new ScreenfeedAdminbarTools_Mustache_Loader_FilesystemLoader(dirname(__FILE__).'/views'),
 *          'partials_loader' => new ScreenfeedAdminbarTools_Mustache_Loader_FilesystemLoader(dirname(__FILE__).'/views/partials'),
 *     ));
 */
class ScreenfeedAdminbarTools_Mustache_Loader_FilesystemLoader implements ScreenfeedAdminbarTools_Mustache_Loader
{
    private $baseDir;
    private $extension = '.mustache';
    private $templates = array();

    /**
     * Mustache filesystem Loader ScreenfeedAdminbarTools_constructor.
     *
     * Passing an $options array allows overriding certain Loader options during instantiation:
     *
     *     $options = array(
     *         // The filename extension used for Mustache templates. Defaults to '.mustache'
     *         'extension' => '.ms',
     *     );
     *
     * @throws ScreenfeedAdminbarTools_Mustache_Exception_RuntimeException if $baseDir does not exist
     *
     * @param string $baseDir Base directory containing Mustache template files
     * @param array  $options Array of Loader options (default: array())
     */
    public function __construct($baseDir, array $options = array())
    {
        $this->baseDir = $baseDir;

        if (strpos($this->baseDir, '://') === false) {
            $this->baseDir = realpath($this->baseDir);
        }

        if ($this->shouldCheckPath() && !is_dir($this->baseDir)) {
            throw new ScreenfeedAdminbarTools_Mustache_Exception_RuntimeException(sprintf('FilesystemLoader baseDir must be a directory: %s', $baseDir));
        }

        if (array_key_exists('extension', $options)) {
            if (empty($options['extension'])) {
                $this->extension = '';
            } else {
                $this->extension = '.' . ltrim($options['extension'], '.');
            }
        }
    }

    /**
     * Load a Template by ScreenfeedAdminbarTools_name.
     *
     *     $loader = new ScreenfeedAdminbarTools_Mustache_Loader_FilesystemLoader(dirname(__FILE__).'/views');
     *     $loader->ScreenfeedAdminbarTools_load('admin/dashboard'); // loads "./views/admin/dashboard.mustache";
     *
     * @param string $ScreenfeedAdminbarTools_name
     *
     * @return string Mustache Template source
     */
    public function ScreenfeedAdminbarTools_load($ScreenfeedAdminbarTools_name)
    {
        if (!isset($this->templates[$ScreenfeedAdminbarTools_name])) {
            $this->templates[$ScreenfeedAdminbarTools_name] = $this->loadFile($ScreenfeedAdminbarTools_name);
        }

        return $this->templates[$ScreenfeedAdminbarTools_name];
    }

    /**
     * Helper function for loading a Mustache file by ScreenfeedAdminbarTools_name.
     *
     * @throws ScreenfeedAdminbarTools_Mustache_Exception_UnknownTemplateException If a template file ScreenfeedAdminbarTools_is not found
     *
     * @param string $ScreenfeedAdminbarTools_name
     *
     * @return string Mustache Template source
     */
    protected function loadFile($ScreenfeedAdminbarTools_name)
    {
        $fileName = $this->getFileName($ScreenfeedAdminbarTools_name);

        if ($this->shouldCheckPath() && !file_exists($fileName)) {
            throw new ScreenfeedAdminbarTools_Mustache_Exception_UnknownTemplateException($ScreenfeedAdminbarTools_name);
        }

        return file_get_contents($fileName);
    }

    /**
     * Helper function for getting a Mustache template file ScreenfeedAdminbarTools_name.
     *
     * @param string $ScreenfeedAdminbarTools_name
     *
     * @return string Template file ScreenfeedAdminbarTools_name
     */
    protected function getFileName($ScreenfeedAdminbarTools_name)
    {
        $fileName = $this->baseDir . '/' . $ScreenfeedAdminbarTools_name;
        if (substr($fileName, 0 - strlen($this->extension)) !== $this->extension) {
            $fileName .= $this->extension;
        }

        return $fileName;
    }

    /**
     * Only check if baseDir ScreenfeedAdminbarTools_is a directory and requested templates are files if
     * baseDir ScreenfeedAdminbarTools_is using the filesystem stream wrapper.
     *
     * @return bool Whether to check `is_dir` and `file_exists`
     */
    protected function shouldCheckPath()
    {
        return strpos($this->baseDir, '://') === false || strpos($this->baseDir, 'file://') === 0;
    }
}
