<?php

namespace Screenfeed\AdminbarTools\Dependencies\League\Container;

interface ContainerAwareInterface
{
    /**
     * Set a container
     *
     * @param \Screenfeed\AdminbarTools\Dependencies\League\Container\ContainerInterface $container
     */
    public function setContainer(ContainerInterface $container);

    /**
     * Get the container
     *
     * @return \Screenfeed\AdminbarTools\Dependencies\League\Container\ContainerInterface
     */
    public function getContainer();
}
