<?php

namespace JobsBundle\Normalizer;

use Doctrine\ORM\EntityManagerInterface;
use JobsBundle\Model\ConnectorContextItem;
use JobsBundle\Model\ConnectorEngine;
use JobsBundle\Model\ContextDefinition;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

class EntityNormalizer implements DenormalizerInterface
{
    protected EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function supportsDenormalization($data, $type, $format = null): bool
    {
        return \is_a($type, ConnectorContextItem::class, true);
    }

    public function denormalize(mixed $data, string $type, ?string $format = null, array $context = []): mixed
    {
        $connectorContextItem = null;

        if (is_numeric($data['id'])) {
            $connectorContextItem = $this->entityManager->find(ConnectorContextItem::class, $data['id']);
        }

        if (!$connectorContextItem instanceof ConnectorContextItem) {
            $connectorContextItem = new ConnectorContextItem();
        }

        $connectorEngine = $this->entityManager->find(ConnectorEngine::class, $data['connectorEngine']['id']);
        if (!$connectorEngine instanceof ConnectorEngine) {
            return null;
        }

        $contextDefinition = $this->entityManager->find(ContextDefinition::class, $data['contextDefinition']['id']);
        if (!$contextDefinition instanceof ContextDefinition) {
            return null;
        }

        $connectorContextItem->setObjectId($data['objectId']);
        $connectorContextItem->setConnectorEngine($connectorEngine);
        $connectorContextItem->setContextDefinition($contextDefinition);

        return $connectorContextItem;
    }
}