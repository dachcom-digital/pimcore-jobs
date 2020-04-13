<?php

namespace JobsBundle\Manager;

use Doctrine\ORM\EntityManagerInterface;
use JobsBundle\Model\ConnectorContextItem;
use JobsBundle\Model\ConnectorContextItemInterface;
use JobsBundle\Repository\ConnectorContextItemRepositoryInterface;

class ConnectorContextManager implements ConnectorContextManagerInterface
{
    /**
     * @var array
     */
    protected $contextDefinitions;

    /**
     * @var ConnectorManagerInterface
     */
    protected $connectorManager;

    /**
     * @var ConnectorContextItemRepositoryInterface
     */
    protected $connectorContextItemRepository;

    /**
     * @var EntityManagerInterface
     */
    protected $entityManager;

    /**
     * @param array                                   $contextDefinitions
     * @param ConnectorManagerInterface               $connectorManager
     * @param ConnectorContextItemRepositoryInterface $connectorContextItemRepository
     * @param EntityManagerInterface                  $entityManager
     */
    public function __construct(
        array $contextDefinitions,
        ConnectorManagerInterface $connectorManager,
        ConnectorContextItemRepositoryInterface $connectorContextItemRepository,
        EntityManagerInterface $entityManager
    ) {
        $this->contextDefinitions = $contextDefinitions;
        $this->connectorManager = $connectorManager;
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
        $items = $this->connectorContextItemRepository->findForConnectorEngineAndObject($connectorEngineId, $objectId);

        foreach ($items as $item) {
            $item->setContextDefinition(array_reduce($this->contextDefinitions,
                function ($result, array $configItem) use ($item) {
                    return $configItem['id'] === $item->getContextDefinitionId() ? $configItem : $result;
                }));
        }

        return $items;
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

        $data = [
            'context'             => [],
            'context_definitions' => $this->contextDefinitions
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
                'contextDefinitionId' => $connectorContextItem->getContextDefinitionId(),
            ];
        }

        $data['context'] = $context;

        return $data;
    }
}
