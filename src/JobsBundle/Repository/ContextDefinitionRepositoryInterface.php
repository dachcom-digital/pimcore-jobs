<?php

namespace JobsBundle\Repository;

use JobsBundle\Model\ContextDefinitionInterface;

interface ContextDefinitionRepositoryInterface
{
    /**
     * @param int $id
     *
     * @return null|ContextDefinitionInterface
     */
    public function findById(int $id);

    /**
     * @return ContextDefinitionInterface[]
     */
    public function findAll();
}
