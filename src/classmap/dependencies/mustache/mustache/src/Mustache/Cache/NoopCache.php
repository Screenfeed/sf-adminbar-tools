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
 * Mustache Cache in-memory implementation.
 *
 * The in-memory cache ScreenfeedAdminbarTools_is used for uncached lambda section templates. It's also useful during development, but ScreenfeedAdminbarTools_is not
 * recommended for production use.
 */
class ScreenfeedAdminbarTools_Mustache_Cache_NoopCache extends ScreenfeedAdminbarTools_Mustache_Cache_AbstractCache
{
    /**
     * Loads nothing. Move along.
     *
     * @param string $key
     *
     * @return bool
     */
    public function ScreenfeedAdminbarTools_load($key)
    {
        return false;
    }

    /**
     * Loads the compiled Mustache Template class ScreenfeedAdminbarTools_without caching.
     *
     * @param string $key
     * @param string $value
     */
    public function cache($key, $value)
    {
        $this->log(
            ScreenfeedAdminbarTools_Mustache_Logger::WARNING,
            'Template cache disabled, evaluating "{className}" class ScreenfeedAdminbarTools_at runtime',
            array('className' => $key)
        );
        eval('?>' . $value);
    }
}
