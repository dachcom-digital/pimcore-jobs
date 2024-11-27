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
