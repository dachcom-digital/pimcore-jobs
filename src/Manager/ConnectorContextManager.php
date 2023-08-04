<?php

namespace JobsBundle\Manager;

use Doctrine\ORM\EntityManagerInterface;
use JobsBundle\Model\ConnectorContextItem;
use JobsBundle\Model\ConnectorContextItemInterface;
use JobsBundle\Model\ContextDefinitionInterface;
use JobsBundle\Repository\ConnectorContextItemRepositoryInterface;

class ConnectorContextManager implements ConnectorContextManagerInterface
{
    public function __construct(
        protected ConnectorManagerInterface $connectorManager,
        protected ContextDefinitionManagerInterface $contextDefinitionManager,
        protected ConnectorContextItemRepositoryInterface $connectorContextItemRepository,
        protected EntityManagerInterface $entityManager
    ) {
    }

    public function getForObject(int $objectId): array
    {
        return $this->connectorContextItemRepository->findForObject($objectId);
    }

    public function getForConnectorEngine(int $connectorEngineId): array
    {
        return $this->connectorContextItemRepository->findForConnectorEngine($connectorEngineId);
    }

    public function getForConnectorEngineAndObject(int $connectorEngineId, int $objectId): array
    {
        return $this->connectorContextItemRepository->findForConnectorEngineAndObject($connectorEngineId, $objectId);
    }

    public function getContextDefinition(int $definitionContextId): ContextDefinitionInterface
    {
        return $this->contextDefinitionManager->getById($definitionContextId);
    }

    public function connectorAllowsMultipleContextItems(string $connectorDefinitionName): bool
    {
        $connectorDefinition = $this->connectorManager->getConnectorDefinition($connectorDefinitionName, false);

        return $connectorDefinition->allowMultipleContextItems();
    }

    public function createNew(int $connectorId): ConnectorContextItemInterface
    {
        $connectorEngine = $this->connectorManager->getEngineById($connectorId);

        $connectorContextItem = new ConnectorContextItem();
        $connectorContextItem->setConnectorEngine($connectorEngine);

        return $connectorContextItem;
    }

    public function update(ConnectorContextItemInterface $connectorContextItem): ConnectorContextItemInterface
    {
        $this->entityManager->persist($connectorContextItem);
        $this->entityManager->flush();

        return $connectorContextItem;
    }

    public function delete(ConnectorContextItemInterface $connectorContextItem): void
    {
        $this->entityManager->remove($connectorContextItem);
        $this->entityManager->flush();
    }

    public function generateConnectorContextConfig(array $connectorContextItems): array
    {
        $context = [];
        $onlineConnectors = [];
        $contextDefinitions = [];

        foreach ($this->contextDefinitionManager->getAll() as $contextDefinition) {
            $contextDefinitions[] = [
                'id'     => $contextDefinition->getId(),
                'locale' => $contextDefinition->getLocale(),
                'host'   => $contextDefinition->getHost(),
            ];
        }

        $data = [
            'context'             => [],
            'context_definitions' => $contextDefinitions
        ];

        $onlineConnectorDefinitions = $this->connectorManager->getAllConnectorDefinitions(true);

        foreach ($onlineConnectorDefinitions as $connectorDefinition) {
            if (!$connectorDefinition->isOnline()) {
                continue;
            }

            $onlineConnectors[] = [
                'id'            => $connectorDefinition->getConnectorEngine()->getId(),
                'name'          => $connectorDefinition->getConnectorEngine()->getName(),
                'label'         => ucfirst($connectorDefinition->getConnectorEngine()->getName()),
                'has_log_panel' => $connectorDefinition->hasLogPanel()
            ];
        }

        $data['connectors'] = $onlineConnectors;

        if (!is_array($connectorContextItems)) {
            return $data;
        }

        foreach ($connectorContextItems as $connectorContextItem) {
            $context[] = [
                'connector'           => [
                    'id'   => $connectorContextItem->getConnectorEngine()->getId(),
                    'name' => $connectorContextItem->getConnectorEngine()->getName()
                ],
                'contextDefinitionId' => $connectorContextItem->getContextDefinition()->getId(),
            ];
        }

        $data['context'] = $context;

        return $data;
    }
}
