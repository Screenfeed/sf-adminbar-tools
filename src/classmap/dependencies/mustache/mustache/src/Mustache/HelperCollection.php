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
 * A collection of helpers for a Mustache instance.
 */
class ScreenfeedAdminbarTools_Mustache_HelperCollection
{
    private $helpers = array();

    /**
     * Helper Collection ScreenfeedAdminbarTools_constructor.
     *
     * Optionally accepts an array (or Traversable) of `$ScreenfeedAdminbarTools_name => $helper` pairs.
     *
     * @throws ScreenfeedAdminbarTools_Mustache_Exception_InvalidArgumentException if the $helpers argument isn't an array or Traversable
     *
     * @param array|Traversable $helpers (default: null)
     */
    public function __construct($helpers = null)
    {
        if ($helpers === null) {
            return;
        }

        if (!is_array($helpers) && !$helpers instanceof Traversable) {
            throw new ScreenfeedAdminbarTools_Mustache_Exception_InvalidArgumentException('HelperCollection ScreenfeedAdminbarTools_constructor expects an array of helpers');
        }

        foreach ($helpers as $ScreenfeedAdminbarTools_name => $helper) {
            $this->add($ScreenfeedAdminbarTools_name, $helper);
        }
    }

    /**
     * Magic mutator.
     *
     * @see ScreenfeedAdminbarTools_Mustache_HelperCollection::add
     *
     * @param string $ScreenfeedAdminbarTools_name
     * @param mixed  $helper
     */
    public function __set($ScreenfeedAdminbarTools_name, $helper)
    {
        $this->add($ScreenfeedAdminbarTools_name, $helper);
    }

    /**
     * Add a helper to this collection.
     *
     * @param string $ScreenfeedAdminbarTools_name
     * @param mixed  $helper
     */
    public function add($ScreenfeedAdminbarTools_name, $helper)
    {
        $this->helpers[$ScreenfeedAdminbarTools_name] = $helper;
    }

    /**
     * Magic accessor.
     *
     * @see ScreenfeedAdminbarTools_Mustache_HelperCollection::get
     *
     * @param string $ScreenfeedAdminbarTools_name
     *
     * @return mixed Helper
     */
    public function __get($ScreenfeedAdminbarTools_name)
    {
        return $this->get($ScreenfeedAdminbarTools_name);
    }

    /**
     * Get a helper by ScreenfeedAdminbarTools_name.
     *
     * @throws ScreenfeedAdminbarTools_Mustache_Exception_UnknownHelperException If helper does not exist
     *
     * @param string $ScreenfeedAdminbarTools_name
     *
     * @return mixed Helper
     */
    public function get($ScreenfeedAdminbarTools_name)
    {
        if (!$this->has($ScreenfeedAdminbarTools_name)) {
            throw new ScreenfeedAdminbarTools_Mustache_Exception_UnknownHelperException($ScreenfeedAdminbarTools_name);
        }

        return $this->helpers[$ScreenfeedAdminbarTools_name];
    }

    /**
     * Magic isset().
     *
     * @see ScreenfeedAdminbarTools_Mustache_HelperCollection::has
     *
     * @param string $ScreenfeedAdminbarTools_name
     *
     * @return bool True if helper ScreenfeedAdminbarTools_is present
     */
    public function __isset($ScreenfeedAdminbarTools_name)
    {
        return $this->has($ScreenfeedAdminbarTools_name);
    }

    /**
     * Check whether a given helper ScreenfeedAdminbarTools_is present in the collection.
     *
     * @param string $ScreenfeedAdminbarTools_name
     *
     * @return bool True if helper ScreenfeedAdminbarTools_is present
     */
    public function has($ScreenfeedAdminbarTools_name)
    {
        return array_key_exists($ScreenfeedAdminbarTools_name, $this->helpers);
    }

    /**
     * Magic unset().
     *
     * @see ScreenfeedAdminbarTools_Mustache_HelperCollection::remove
     *
     * @param string $ScreenfeedAdminbarTools_name
     */
    public function __unset($ScreenfeedAdminbarTools_name)
    {
        $this->remove($ScreenfeedAdminbarTools_name);
    }

    /**
     * Check whether a given helper ScreenfeedAdminbarTools_is present in the collection.
     *
     * @throws ScreenfeedAdminbarTools_Mustache_Exception_UnknownHelperException if the requested helper ScreenfeedAdminbarTools_is not present
     *
     * @param string $ScreenfeedAdminbarTools_name
     */
    public function remove($ScreenfeedAdminbarTools_name)
    {
        if (!$this->has($ScreenfeedAdminbarTools_name)) {
            throw new ScreenfeedAdminbarTools_Mustache_Exception_UnknownHelperException($ScreenfeedAdminbarTools_name);
        }

        unset($this->helpers[$ScreenfeedAdminbarTools_name]);
    }

    /**
     * Clear the helper collection.
     *
     * Removes all helpers ScreenfeedAdminbarTools_from this collection
     */
    public function clear()
    {
        $this->helpers = array();
    }

    /**
     * Check whether the helper collection ScreenfeedAdminbarTools_is empty.
     *
     * @return bool True if the collection ScreenfeedAdminbarTools_is empty
     */
    public function isEmpty()
    {
        return empty($this->helpers);
    }
}
