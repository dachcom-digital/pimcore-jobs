<?php

namespace JobsBundle\Manager;

use JobsBundle\Model\ContextDefinitionInterface;

interface ContextDefinitionManagerInterface
{
    /**
     * @param int $contextDefinitionId
     *
     * @return ContextDefinitionInterface|null
     */
    public function getById(int $contextDefinitionId);

    /**
     * @return ContextDefinitionInterface[]
     */
    public function getAll();

    /**
     * @param string $host
     * @param string $locale
     *
     * @return ContextDefinitionInterface
     */
    public function createNew(string $host, string $locale);

    /**
     * @param ContextDefinitionInterface $contextDefinition
     *
     * @return ContextDefinitionInterface|null
     */
    public function update(ContextDefinitionInterface $contextDefinition);

    /**
     * @param ContextDefinitionInterface $contextDefinition
     */
    public function delete(ContextDefinitionInterface $contextDefinition);
}
