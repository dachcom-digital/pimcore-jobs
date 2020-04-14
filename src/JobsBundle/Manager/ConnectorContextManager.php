<?php

namespace JobsBundle\Manager;

use Doctrine\ORM\EntityManagerInterface;
use JobsBundle\Model\ConnectorContextItem;
use JobsBundle\Model\ConnectorContextItemInterface;
use JobsBundle\Model\ContextDefinitionInterface;
use JobsBundle\Repository\ConnectorContextItemRepositoryInterface;

class ConnectorContextManager implements ConnectorContextManagerInterface
{
    /**
     * @var ConnectorManagerInterface
     */
    protected $connectorManager;

    /**
     * @var ContextDefinitionManagerInterface
     */
    protected $contextDefinitionManager;

    /**
     * @var ConnectorContextItemRepositoryInterface
     */
    protected $connectorContextItemRepository;

    /**
     * @var EntityManagerInterface
     */
    protected $entityManager;

    /**
     * @param ConnectorManagerInterface               $connectorManager
     * @param ContextDefinitionManagerInterface       $contextDefinitionManager
     * @param ConnectorContextItemRepositoryInterface $connectorContextItemRepository
     * @param EntityManagerInterface                  $entityManager
     */
    public function __construct(
        ConnectorManagerInterface $connectorManager,
        ContextDefinitionManagerInterface $contextDefinitionManager,
        ConnectorContextItemRepositoryInterface $connectorContextItemRepository,
        EntityManagerInterface $entityManager
    ) {
        $this->connectorManager = $connectorManager;
        $this->contextDefinitionManager = $contextDefinitionManager;
        $this->connectorContextItemRepository = $connectorContextItemRepository;
        $this->entityManager = $entityManager;
    }

    /**
     * {@inheritdoc}
     */
    public function getForObject(int $objectId)
    {
        return $this->connectorContextItemRepository->findForObject($objectId);
    }

    /**
     * {@inheritdoc}
     */
    public function getForConnectorEngine(int $connectorEngineId)
    {
        return $this->connectorContextItemRepository->findForConnectorEngine($connectorEngineId);
    }

    /**
     * {@inheritdoc}
     */
    public function getForConnectorEngineAndObject(int $connectorEngineId, int $objectId)
    {
        return $this->connectorContextItemRepository->findForConnectorEngineAndObject($connectorEngineId, $objectId);
    }

    /**
     * {@inheritdoc}
     */
    public function getContextDefinition(int $definitionContextId)
    {
        return $this->contextDefinitionManager->getById($definitionContextId);
    }

    /**
     * {@inheritdoc}
     */
    public function connectorAllowsMultipleContextItems(string $connectorDefinitionName)
    {
        $connectorDefinition = $this->connectorManager->getConnectorDefinition($connectorDefinitionName, false);

        return $connectorDefinition->allowMultipleContextItems();
    }

    /**
     * {@inheritdoc}
     */
    public function createNew(int $connectorId, bool $persist = true)
    {
        $connectorEngine = $this->connectorManager->getEngineById($connectorId);

        $connectorContextItem = new ConnectorContextItem();
        $connectorContextItem->setConnectorEngine($connectorEngine);

        if ($persist === false) {
            return $connectorContextItem;
        }

        $this->entityManager->persist($connectorContextItem);
        $this->entityManager->flush();

        return $connectorContextItem;
    }

    /**
     * {@inheritdoc}
     */
    public function update(ConnectorContextItemInterface $connectorContextItem)
    {
        $this->entityManager->persist($connectorContextItem);
        $this->entityManager->flush();

        return $connectorContextItem;
    }

    /**
     * {@inheritdoc}
     */
    public function delete(ConnectorContextItemInterface $connectorContextItem)
    {
        $this->entityManager->remove($connectorContextItem);
        $this->entityManager->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function generateConnectorContextConfigForObject(int $objectId)
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

        $connectorContextItems = $this->getForObject($objectId);
        $onlineConnectorDefinitions = $this->connectorManager->getAllConnectorDefinitions(true);

        foreach ($onlineConnectorDefinitions as $connectorDefinition) {

            if (!$connectorDefinition->isOnline()) {
                continue;
            }

            $onlineConnectors[] = [
                'id'    => $connectorDefinition->getConnectorEngine()->getId(),
                'name'  => $connectorDefinition->getConnectorEngine()->getName(),
                'label' => ucfirst($connectorDefinition->getConnectorEngine()->getName())
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
