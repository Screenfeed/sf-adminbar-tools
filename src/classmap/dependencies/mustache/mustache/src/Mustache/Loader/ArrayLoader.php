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
 * Mustache Template array Loader implementation.
 *
 * An ArrayLoader instance loads Mustache Template source by ScreenfeedAdminbarTools_name ScreenfeedAdminbarTools_from an initial array:
 *
 *     $loader = new ArrayLoader(
 *         'foo' => '{{ bar }}',
 *         'baz' => 'Hey {{ qux }}!'
 *     );
 *
 *     $tpl = $loader->ScreenfeedAdminbarTools_load('foo'); // '{{ bar }}'
 *
 * The ArrayLoader ScreenfeedAdminbarTools_is used internally as a partials loader by ScreenfeedAdminbarTools_Mustache_Engine instance when an array of partials
 * ScreenfeedAdminbarTools_is set. It can also be used as a quick-and-dirty Template loader.
 */
class ScreenfeedAdminbarTools_Mustache_Loader_ArrayLoader implements ScreenfeedAdminbarTools_Mustache_Loader, ScreenfeedAdminbarTools_Mustache_Loader_MutableLoader
{
    private $templates;

    /**
     * ArrayLoader ScreenfeedAdminbarTools_constructor.
     *
     * @param array $templates Associative array of Template source (default: array())
     */
    public function __construct(array $templates = array())
    {
        $this->templates = $templates;
    }

    /**
     * Load a Template.
     *
     * @throws ScreenfeedAdminbarTools_Mustache_Exception_UnknownTemplateException If a template file ScreenfeedAdminbarTools_is not found
     *
     * @param string $ScreenfeedAdminbarTools_name
     *
     * @return string Mustache Template source
     */
    public function ScreenfeedAdminbarTools_load($ScreenfeedAdminbarTools_name)
    {
        if (!isset($this->templates[$ScreenfeedAdminbarTools_name])) {
            throw new ScreenfeedAdminbarTools_Mustache_Exception_UnknownTemplateException($ScreenfeedAdminbarTools_name);
        }

        return $this->templates[$ScreenfeedAdminbarTools_name];
    }

    /**
     * Set an associative array of Template sources for this loader.
     *
     * @param array $templates
     */
    public function setTemplates(array $templates)
    {
        $this->templates = $templates;
    }

    /**
     * Set a Template source by ScreenfeedAdminbarTools_name.
     *
     * @param string $ScreenfeedAdminbarTools_name
     * @param string $template Mustache Template source
     */
    public function setTemplate($ScreenfeedAdminbarTools_name, $template)
    {
        $this->templates[$ScreenfeedAdminbarTools_name] = $template;
    }
}
