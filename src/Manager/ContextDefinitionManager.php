<?php

/*
 * This source file is available under two different licenses:
 *   - GNU General Public License version 3 (GPLv3)
 *   - DACHCOM Commercial License (DCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) DACHCOM.DIGITAL AG (https://www.dachcom-digital.com)
 * @license    GPLv3 and DCL
 */

namespace JobsBundle\Manager;

use Doctrine\ORM\EntityManagerInterface;
use JobsBundle\Model\ContextDefinition;
use JobsBundle\Model\ContextDefinitionInterface;
use JobsBundle\Repository\ContextDefinitionRepositoryInterface;

class ContextDefinitionManager implements ContextDefinitionManagerInterface
{
    public function __construct(
        protected ContextDefinitionRepositoryInterface $contextDefinitionRepository,
        protected EntityManagerInterface $entityManager
    ) {
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
