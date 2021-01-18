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
 * A Mustache Template cascading loader implementation, which delegates to other
 * Loader instances.
 */
class ScreenfeedAdminbarTools_Mustache_Loader_CascadingLoader implements ScreenfeedAdminbarTools_Mustache_Loader
{
    private $loaders;

    /**
     * Construct a CascadingLoader with an array of loaders.
     *
     *     $loader = new ScreenfeedAdminbarTools_Mustache_Loader_CascadingLoader(array(
     *         new ScreenfeedAdminbarTools_Mustache_Loader_InlineLoader(__FILE__, __COMPILER_HALT_OFFSET__),
     *         new ScreenfeedAdminbarTools_Mustache_Loader_FilesystemLoader(__DIR__.'/templates')
     *     ));
     *
     * @param ScreenfeedAdminbarTools_Mustache_Loader[] $loaders
     */
    public function __construct(array $loaders = array())
    {
        $this->loaders = array();
        foreach ($loaders as $loader) {
            $this->addLoader($loader);
        }
    }

    /**
     * Add a Loader instance.
     *
     * @param ScreenfeedAdminbarTools_Mustache_Loader $loader
     */
    public function addLoader(ScreenfeedAdminbarTools_Mustache_Loader $loader)
    {
        $this->loaders[] = $loader;
    }

    /**
     * Load a Template by ScreenfeedAdminbarTools_name.
     *
     * @throws ScreenfeedAdminbarTools_Mustache_Exception_UnknownTemplateException If a template file ScreenfeedAdminbarTools_is not found
     *
     * @param string $ScreenfeedAdminbarTools_name
     *
     * @return string Mustache Template source
     */
    public function ScreenfeedAdminbarTools_load($ScreenfeedAdminbarTools_name)
    {
        foreach ($this->loaders as $loader) {
            try {
                return $loader->ScreenfeedAdminbarTools_load($ScreenfeedAdminbarTools_name);
            } catch (ScreenfeedAdminbarTools_Mustache_Exception_UnknownTemplateException $e) {
                // do nothing, check the next loader.
            }
        }

        throw new ScreenfeedAdminbarTools_Mustache_Exception_UnknownTemplateException($ScreenfeedAdminbarTools_name);
    }
}
