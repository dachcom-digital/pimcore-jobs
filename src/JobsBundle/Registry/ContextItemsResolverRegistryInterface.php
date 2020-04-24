<?php

namespace JobsBundle\Registry;

use JobsBundle\Context\Resolver\ContextItemsResolverInterface;

interface ContextItemsResolverRegistryInterface
{
    /**
     * @param string $identifier
     *
     * @return bool
     */
    public function has($identifier);

    /**
     * @param string $identifier
     *
     * @return ContextItemsResolverInterface
     *
     * @throws \Exception
     */
    public function get($identifier);

    /**
     * @return ContextItemsResolverInterface[]
     */
    public function getAll();
}
