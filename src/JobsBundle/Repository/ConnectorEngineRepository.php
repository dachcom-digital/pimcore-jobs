<?php

namespace JobsBundle\Repository;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use JobsBundle\Model\ConnectorEngine;
use JobsBundle\Model\ConnectorEngineInterface;

class ConnectorEngineRepository implements ConnectorEngineRepositoryInterface
{
    protected EntityRepository $repository;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->repository = $entityManager->getRepository(ConnectorEngine::class);
    }

    public function findById($id): ?ConnectorEngineInterface
    {
        if ($id < 1) {
            return null;
        }

        return $this->repository->find($id);
    }

    public function findByName(string $name): ?ConnectorEngineInterface
    {
        if (empty($name)) {
            return null;
        }

        return $this->repository->findOneBy(['name' => $name]);
    }

    public function findIdByName(string $name): ?ConnectorEngineInterface
    {
        $form = $this->findByName($name);

        return $form->getId();
    }

    public function findAll(): array
    {
        return $this->repository->findAll();
    }
}
