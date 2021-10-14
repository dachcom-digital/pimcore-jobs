<?php

namespace JobsBundle\Manager;

use JobsBundle\Model\ContextDefinition;
use JobsBundle\Model\ContextDefinitionInterface;
use JobsBundle\Repository\ContextDefinitionRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;

class ContextDefinitionManager implements ContextDefinitionManagerInterface
{
    protected ContextDefinitionRepositoryInterface $contextDefinitionRepository;
    protected EntityManagerInterface $entityManager;

    public function __construct(
        ContextDefinitionRepositoryInterface $contextDefinitionRepository,
        EntityManagerInterface $entityManager
    ) {
        $this->contextDefinitionRepository = $contextDefinitionRepository;
        $this->entityManager = $entityManager;
    }

    public function createNew(string $host, string $locale): ContextDefinitionInterface
    {
        $contextDefinition = new ContextDefinition();
        $contextDefinition->setHost($host);
        $contextDefinition->setLocale($locale);

        $this->entityManager->persist($contextDefinition);
        $this->entityManager->flush();

        return $contextDefinition;
    }

    public function getById(int $contextDefinitionId): ?ContextDefinitionInterface
    {
        return $this->contextDefinitionRepository->findById($contextDefinitionId);
    }

    /**
     * {@inheritdoc}
     */
    public function getAll(): array
    {
        return $this->contextDefinitionRepository->findAll();
    }

    public function update(ContextDefinitionInterface $contextDefinition): ContextDefinitionInterface
    {
        $this->entityManager->persist($contextDefinition);
        $this->entityManager->flush();

        return $contextDefinition;
    }

    public function delete(ContextDefinitionInterface $contextDefinition): void
    {
        $this->entityManager->remove($contextDefinition);
        $this->entityManager->flush();
    }
}
