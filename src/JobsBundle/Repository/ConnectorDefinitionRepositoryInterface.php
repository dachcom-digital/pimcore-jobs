<?php

namespace JobsBundle\Repository;

use JobsBundle\Model\ConnectorDefinitionInterface;

interface ConnectorDefinitionRepositoryInterface
{
    /**
     * @param int $id
     *
     * @return null|ConnectorDefinitionInterface
     */
    public function findById($id);

    /**
     * @param string $name
     *
     * @return null|ConnectorDefinitionInterface
     */
    public function findByName(string $name);

    /**
     * @param string $name
     *
     * @return null|ConnectorDefinitionInterface
     */
    public function findIdByName(string $name);

    /**
     * @return ConnectorDefinitionInterface[]
     */
    public function findAll();
}
