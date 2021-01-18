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
 * Mustache Template Loader interface.
 */
interface ScreenfeedAdminbarTools_Mustache_Loader
{
    /**
     * Load a Template by ScreenfeedAdminbarTools_name.
     *
     * @throws ScreenfeedAdminbarTools_Mustache_Exception_UnknownTemplateException If a template file ScreenfeedAdminbarTools_is not found
     *
     * @param string $ScreenfeedAdminbarTools_name
     *
     * @return string|ScreenfeedAdminbarTools_Mustache_Source Mustache Template source
     */
    public function ScreenfeedAdminbarTools_load($ScreenfeedAdminbarTools_name);
}
