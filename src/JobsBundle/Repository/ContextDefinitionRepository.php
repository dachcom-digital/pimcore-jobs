<?php

namespace JobsBundle\Repository;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use JobsBundle\Model\ContextDefinition;

class ContextDefinitionRepository implements ContextDefinitionRepositoryInterface
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
        $this->repository = $entityManager->getRepository(ContextDefinition::class);
    }

    /**
     * {@inheritdoc}
     */
    public function findById(int $id)
    {
        return $this->repository->find($id);
    }

    /**
     * {@inheritdoc}
     */
    public function findAll()
    {
        return $this->repository->findAll();
    }
}
