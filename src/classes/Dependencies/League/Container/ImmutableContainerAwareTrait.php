<?php

namespace Screenfeed\AdminbarTools\Dependencies\League\Container;

use Screenfeed\AdminbarTools\Dependencies\Interop\Container\ContainerInterface as InteropContainerInterface;

trait ImmutableContainerAwareTrait
{
    /**
     * @var \Screenfeed\AdminbarTools\Dependencies\Interop\Container\ContainerInterface
     */
    protected $container;

    /**
     * Set a container.
     *
     * @param  \Screenfeed\AdminbarTools\Dependencies\Interop\Container\ContainerInterface $container
     * @return $this
     */
    public function setContainer(InteropContainerInterface $container)
    {
        $this->container = $container;

        return $this;
    }

    /**
     * Get the container.
     *
     * @return \Screenfeed\AdminbarTools\Dependencies\League\Container\ImmutableContainerInterface
     */
    public function getContainer()
    {
        return $this->container;
    }
}
