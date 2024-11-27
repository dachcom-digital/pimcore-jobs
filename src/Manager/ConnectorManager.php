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
use JobsBundle\Connector\ConnectorDefinitionInterface;
use JobsBundle\Model\ConnectorEngine;
use JobsBundle\Model\ConnectorEngineInterface;
use JobsBundle\Registry\ConnectorDefinitionRegistryInterface;
use JobsBundle\Repository\ConnectorEngineRepositoryInterface;
use Symfony\Component\Uid\Uuid;

class ConnectorManager implements ConnectorManagerInterface
{
    public function __construct(
        protected array $availableConnectors,
        protected ConnectorDefinitionRegistryInterface $connectorDefinitionRegistry,
        protected ConnectorEngineRepositoryInterface $connectorEngineRepository,
        protected EntityManagerInterface $entityManager
    ) {
    }

    public function connectorDefinitionIsEnabled(string $connectorDefinitionName): bool
    {
        if (!in_array($connectorDefinitionName, $this->availableConnectors, true)) {
            return false;
        }

        try {
            $this->connectorDefinitionRegistry->get($connectorDefinitionName);
        } catch (\Exception $e) {
            return false;
        }

        return true;
    }

    public function getAllConnectorDefinitions(bool $loadEngine = false): array
    {
        $definitions = [];
        $allConnectorDefinitions = $this->connectorDefinitionRegistry->getAll();

        foreach ($allConnectorDefinitions as $connectorDefinitionName => $connectorDefinition) {
            if (!in_array($connectorDefinitionName, $this->availableConnectors, true)) {
                continue;
            }

            if ($loadEngine === true) {
                $connectorEngine = $this->connectorEngineRepository->findByName($connectorDefinitionName);
                $connectorDefinition->setConnectorEngine($connectorEngine);
            }

            $definitions[$connectorDefinitionName] = $connectorDefinition;
        }

        return $definitions;
    }

    public function getConnectorDefinition(string $connectorDefinitionName, bool $loadEngine = false): ?ConnectorDefinitionInterface
    {
        if (!in_array($connectorDefinitionName, $this->availableConnectors, true)) {
            return null;
        }

        try {
            $connectorDefinition = $this->connectorDefinitionRegistry->get($connectorDefinitionName);
        } catch (\Exception $e) {
            return null;
        }

        if ($loadEngine === true) {
            $connectorEngine = $this->connectorEngineRepository->findByName($connectorDefinitionName);
            $connectorDefinition->setConnectorEngine($connectorEngine);
        }

        return $connectorDefinition;
    }

    public function getEngineById(int $id): ?ConnectorEngineInterface
    {
        return $this->connectorEngineRepository->findById($id);
    }

    public function getEngineByName(string $connectorName): ?ConnectorEngineInterface
    {
        return $this->connectorEngineRepository->findByName($connectorName);
    }

    public function createNewEngine(string $connectorName, $token = null, bool $persist = true): ConnectorEngineInterface
    {
        if ($token === null) {
            $token = (Uuid::v4())->toRfc4122();
        }

        $connector = new ConnectorEngine();
        $connector->setName($connectorName);
        $connector->setToken($token);
        $connector->setEnabled(false);

        if ($persist === false) {
            return $connector;
        }

        $this->entityManager->persist($connector);
        $this->entityManager->flush();

        return $connector;
    }

    public function updateEngine(ConnectorEngineInterface $connector): ?ConnectorEngineInterface
    {
        $this->entityManager->persist($connector);
        $this->entityManager->flush();

        return $connector;
    }

    public function deleteEngine(ConnectorEngineInterface $connector): void
    {
        $this->entityManager->remove($connector);
        $this->entityManager->flush();
    }

    public function deleteEngineByName(string $connectorName): void
    {
        $connector = $this->getEngineByName($connectorName);
        if (!$connector instanceof ConnectorEngineInterface) {
            return;
        }

        $this->entityManager->remove($connector);
        $this->entityManager->flush();
    }
}
