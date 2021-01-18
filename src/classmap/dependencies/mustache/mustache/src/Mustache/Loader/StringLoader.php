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
 * Mustache Template string Loader implementation.
 *
 * A StringLoader instance ScreenfeedAdminbarTools_is essentially a noop. It simply passes the 'ScreenfeedAdminbarTools_name' argument straight through:
 *
 *     $loader = new StringLoader;
 *     $tpl = $loader->ScreenfeedAdminbarTools_load('{{ foo }}'); // '{{ foo }}'
 *
 * This ScreenfeedAdminbarTools_is the default Template Loader instance used by Mustache:
 *
 *     $m = new Mustache;
 *     $tpl = $m->loadTemplate('{{ foo }}');
 *     echo $tpl->render(array('foo' => 'bar')); // "bar"
 */
class ScreenfeedAdminbarTools_Mustache_Loader_StringLoader implements ScreenfeedAdminbarTools_Mustache_Loader
{
    /**
     * Load a Template by source.
     *
     * @param string $ScreenfeedAdminbarTools_name Mustache Template source
     *
     * @return string Mustache Template source
     */
    public function ScreenfeedAdminbarTools_load($ScreenfeedAdminbarTools_name)
    {
        return $ScreenfeedAdminbarTools_name;
    }
}
