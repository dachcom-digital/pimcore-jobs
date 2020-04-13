<?php

namespace JobsBundle\Manager;

use JobsBundle\Model\ContextDefinition;
use JobsBundle\Model\ContextDefinitionInterface;
use JobsBundle\Repository\ContextDefinitionRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;

class ContextDefinitionManager implements ContextDefinitionManagerInterface
{
    /**
     * @var ContextDefinitionRepositoryInterface
     */
    protected $contextDefinitionRepository;

    /**
     * @var EntityManagerInterface
     */
    protected $entityManager;

    /**
     * @param ContextDefinitionRepositoryInterface $contextDefinitionRepository
     * @param EntityManagerInterface               $entityManager
     */
    public function __construct(
        ContextDefinitionRepositoryInterface $contextDefinitionRepository,
        EntityManagerInterface $entityManager
    ) {
        $this->contextDefinitionRepository = $contextDefinitionRepository;
        $this->entityManager = $entityManager;
    }

    /**
     * {@inheritdoc}
     */
    public function createNew(string $host, string $locale)
    {
        $contextDefinition = new ContextDefinition();
        $contextDefinition->setHost($host);
        $contextDefinition->setLocale($locale);

        $this->entityManager->persist($contextDefinition);
        $this->entityManager->flush();

        return $contextDefinition;
    }

    /**
     * {@inheritdoc}
     */
    public function getById(int $contextDefinitionId)
    {
        return $this->contextDefinitionRepository->findById($contextDefinitionId);
    }

    /**
     * {@inheritdoc}
     */
    public function getAll()
    {
        return $this->contextDefinitionRepository->findAll();
    }

    /**
     * {@inheritdoc}
     */
    public function update(ContextDefinitionInterface $contextDefinition)
    {
        $this->entityManager->persist($contextDefinition);
        $this->entityManager->flush();

        return $contextDefinition;
    }

    /**
     * {@inheritdoc}
     */
    public function delete(ContextDefinitionInterface $contextDefinition)
    {
        $this->entityManager->remove($contextDefinition);
        $this->entityManager->flush();
    }
}
