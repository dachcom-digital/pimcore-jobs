<?php

namespace JobsBundle\Repository;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use JobsBundle\Model\ConnectorDefinition;

class ConnectorDefinitionRepository implements ConnectorDefinitionRepositoryInterface
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
        $this->repository = $entityManager->getRepository(ConnectorDefinition::class);
    }

    /**
     * {@inheritdoc}
     */
    public function findById($id)
    {
        if ($id < 1) {
            return null;
        }

        return $this->repository->find($id);
    }

    /**
     * {@inheritdoc}
     */
    public function findByName(string $name)
    {
        if (empty($name)) {
            return null;
        }

        return $this->repository->findOneBy(['name' => $name]);
    }

    /**
     * {@inheritdoc}
     */
    public function findIdByName(string $name)
    {
        $form = $this->findByName($name);

        return $form->getId();
    }

    /**
     * {@inheritdoc}
     */
    public function findAll(): array
    {
        return $this->repository->findAll();
    }
}
