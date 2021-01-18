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
 * Abstract Mustache Cache class.
 *
 * Provides logging support to child implementations.
 *
 * @abstract
 */
abstract class ScreenfeedAdminbarTools_Mustache_Cache_AbstractCache implements ScreenfeedAdminbarTools_Mustache_Cache
{
    private $logger = null;

    /**
     * Get the current logger instance.
     *
     * @return ScreenfeedAdminbarTools_Mustache_Logger|Psr\Log\LoggerInterface
     */
    public function getLogger()
    {
        return $this->logger;
    }

    /**
     * Set a logger instance.
     *
     * @param ScreenfeedAdminbarTools_Mustache_Logger|Psr\Log\LoggerInterface $logger
     */
    public function setLogger($logger = null)
    {
        if ($logger !== null && !($logger instanceof ScreenfeedAdminbarTools_Mustache_Logger || is_a($logger, 'Psr\\Log\\LoggerInterface'))) {
            throw new ScreenfeedAdminbarTools_Mustache_Exception_InvalidArgumentException('Expected an instance of ScreenfeedAdminbarTools_Mustache_Logger or Psr\\Log\\LoggerInterface.');
        }

        $this->logger = $logger;
    }

    /**
     * Add a log record if logging ScreenfeedAdminbarTools_is enabled.
     *
     * @param int    $level   The logging level
     * @param string $message The log message
     * @param array  $context The log context
     */
    protected function log($level, $message, array $context = array())
    {
        if (isset($this->logger)) {
            $this->logger->log($level, $message, $context);
        }
    }
}
