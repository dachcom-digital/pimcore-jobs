<?php

namespace JobsBundle\Repository;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use JobsBundle\Model\ConnectorContextItem;

class ConnectorContextItemRepository implements ConnectorContextItemRepositoryInterface
{
    /**
     * @var EntityRepository
     */
    protected $repository;

    /**
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->repository = $entityManager->getRepository(ConnectorContextItem::class);
    }

    /**
     * {@inheritdoc}
     */
    public function findForObject(int $objectId)
    {
        return $this->repository->findBy(['objectId' => $objectId]);
    }

    /**
     * {@inheritdoc}
     */
    public function findForConnectorEngine(int $connectorEngineId)
    {
        return $this->repository->findBy(['connectorEngine' => $connectorEngineId]);
    }

    /**
     * {@inheritdoc}
     */
    public function findForConnectorEngineAndObject(int $connectorEngineId, int $objectId)
    {
        return $this->repository->findBy(['objectId' => $objectId, 'connectorEngine' => $connectorEngineId]);
    }

    /**
     * {@inheritdoc}
     */
    public function findById(int $id)
    {
        return $this->repository->find($id);
    }
}
