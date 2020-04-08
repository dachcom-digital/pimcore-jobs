<?php

namespace JobsBundle\Manager;

use Doctrine\ORM\EntityManagerInterface;
use JobsBundle\Model\ConnectorDefinition;
use JobsBundle\Model\ConnectorDefinitionInterface;
use JobsBundle\Repository\ConnectorDefinitionRepositoryInterface;
use Ramsey\Uuid\Uuid;

class ConnectorDefinitionManager implements ConnectorDefinitionManagerInterface
{
    /**
     * @var ConnectorDefinitionRepositoryInterface
     */
    protected $connectorDefinitionRepository;

    /**
     * @var EntityManagerInterface
     */
    protected $entityManager;

    /**
     * @param ConnectorDefinitionRepositoryInterface $connectorDefinitionRepository
     * @param EntityManagerInterface                 $entityManager
     */
    public function __construct(
        ConnectorDefinitionRepositoryInterface $connectorDefinitionRepository,
        EntityManagerInterface $entityManager
    ) {
        $this->connectorDefinitionRepository = $connectorDefinitionRepository;
        $this->entityManager = $entityManager;
    }

    /**
     * @param int $id
     *
     * @return ConnectorDefinitionInterface|null
     */
    public function getById(int $id)
    {
        return $this->connectorDefinitionRepository->findById($id);
    }

    /**
     * @param string $connectorDefinitionName
     *
     * @return ConnectorDefinitionInterface|null
     */
    public function getByName(string $connectorDefinitionName)
    {
        return $this->connectorDefinitionRepository->findByName($connectorDefinitionName);
    }

    public function createNew(string $connectorDefinitionName, $token = null)
    {
        if ($token === null) {
            $token = Uuid::uuid4();
        }

        $connectorDefinition = new ConnectorDefinition();
        $connectorDefinition->setName($connectorDefinitionName);
        $connectorDefinition->setToken($token);
        $connectorDefinition->setEnabled(false);

        $this->entityManager->persist($connectorDefinition);
        $this->entityManager->flush();

        return $connectorDefinition;
    }

    /**
     * @param ConnectorDefinitionInterface $connectorDefinition
     *
     * @return ConnectorDefinitionInterface|null
     *
     * @throws \Exception
     */
    public function update(ConnectorDefinitionInterface $connectorDefinition)
    {
        $this->entityManager->persist($connectorDefinition);
        $this->entityManager->flush();

        return $connectorDefinition;
    }

    /**
     * @param string $connectorDefinitionName
     */
    public function deleteByName(string $connectorDefinitionName)
    {
        $connectorDefinition = $this->getByName($connectorDefinitionName);

        if (!$connectorDefinition instanceof ConnectorDefinitionInterface) {
            return;
        }

        $this->entityManager->remove($connectorDefinition);
        $this->entityManager->flush();
    }

    /**
     * @param int $id
     */
    public function delete($id)
    {
        $connector = $this->getById($id);

        if (!$connector instanceof ConnectorDefinitionInterface) {
            return;
        }

        $this->entityManager->remove($connector);
        $this->entityManager->flush();
    }
}
