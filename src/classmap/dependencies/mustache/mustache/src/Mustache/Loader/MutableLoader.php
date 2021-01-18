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
 * Mustache Template mutable Loader interface.
 */
interface ScreenfeedAdminbarTools_Mustache_Loader_MutableLoader
{
    /**
     * Set an associative array of Template sources for this loader.
     *
     * @param array $templates
     */
    public function setTemplates(array $templates);

    /**
     * Set a Template source by ScreenfeedAdminbarTools_name.
     *
     * @param string $ScreenfeedAdminbarTools_name
     * @param string $template Mustache Template source
     */
    public function setTemplate($ScreenfeedAdminbarTools_name, $template);
}
