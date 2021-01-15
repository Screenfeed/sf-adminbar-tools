<?php

namespace Screenfeed\AdminbarTools\Dependencies\League\Container;

use Screenfeed\AdminbarTools\Dependencies\Interop\Container\ContainerInterface as InteropContainerInterface;

interface ImmutableContainerAwareInterface
{
    /**
     * Set a container
     *
     * @param \Screenfeed\AdminbarTools\Dependencies\Interop\Container\ContainerInterface $container
     */
    public function setContainer(InteropContainerInterface $container);

    /**
     * Get the container
     *
     * @return \Screenfeed\AdminbarTools\Dependencies\League\Container\ImmutableContainerInterface
     */
    public function getContainer();
}
