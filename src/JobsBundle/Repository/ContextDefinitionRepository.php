<?php

namespace JobsBundle\Repository;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use JobsBundle\Model\ContextDefinition;
use JobsBundle\Model\ContextDefinitionInterface;

class ContextDefinitionRepository implements ContextDefinitionRepositoryInterface
{
    protected EntityRepository $repository;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->repository = $entityManager->getRepository(ContextDefinition::class);
    }

    public function findById(int $id): ?ContextDefinitionInterface
    {
        return $this->repository->find($id);
    }

    public function findAll(): array
    {
        return $this->repository->findAll();
    }
}
