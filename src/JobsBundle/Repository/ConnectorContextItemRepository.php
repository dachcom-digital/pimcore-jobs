<?php

namespace JobsBundle\Repository;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use JobsBundle\Model\ConnectorContextItem;
use JobsBundle\Model\ConnectorContextItemInterface;

class ConnectorContextItemRepository implements ConnectorContextItemRepositoryInterface
{
    protected EntityRepository $repository;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->repository = $entityManager->getRepository(ConnectorContextItem::class);
    }

    public function findById(int $id): ?ConnectorContextItemInterface
    {
        return $this->repository->find($id);
    }

    public function findForObject(int $objectId): array
    {
        return $this->repository->findBy(['objectId' => $objectId]);
    }

    public function findForConnectorEngine(int $connectorEngineId): array
    {
        return $this->repository->findBy(['connectorEngine' => $connectorEngineId]);
    }

    public function findForConnectorEngineAndObject(int $connectorEngineId, int $objectId): array
    {
        return $this->repository->findBy(['objectId' => $objectId, 'connectorEngine' => $connectorEngineId]);
    }

}
