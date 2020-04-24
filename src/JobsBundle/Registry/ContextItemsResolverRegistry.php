<?php

namespace JobsBundle\Registry;

use JobsBundle\Context\Resolver\ContextItemsResolverInterface;

class ContextItemsResolverRegistry implements ContextItemsResolverRegistryInterface
{
    /**
     * @var array
     */
    protected $resolver;

    /**
     * @param ContextItemsResolverInterface $service
     * @param string                        $identifier
     */
    public function register($service, $identifier)
    {
        if (!in_array(ContextItemsResolverInterface::class, class_implements($service), true)) {
            throw new \InvalidArgumentException(
                sprintf('%s needs to implement "%s", "%s" given.', get_class($service), ContextItemsResolverInterface::class, implode(', ', class_implements($service)))
            );
        }

        $this->resolver[$identifier] = $service;
    }

    /**
     * {@inheritdoc}
     */
    public function has($identifier)
    {
        return isset($this->resolver[$identifier]);
    }

    /**
     * {@inheritdoc}
     */
    public function get($identifier)
    {
        if (!$this->has($identifier)) {
            throw new \Exception('Context Items Resolver "' . $identifier . '" does not exist');
        }

        return $this->resolver[$identifier];
    }

    /**
     * {@inheritdoc}
     */
    public function getAll()
    {
        return $this->resolver;
    }
}
