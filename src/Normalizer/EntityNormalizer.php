<?php

namespace JobsBundle\Normalizer;

use Doctrine\ORM\EntityManagerInterface;
use JobsBundle\Model\ConnectorContextItem;
use JobsBundle\Model\ConnectorEngine;
use JobsBundle\Model\ContextDefinition;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

class EntityNormalizer implements DenormalizerInterface
{
    public function __construct(protected EntityManagerInterface $entityManager)
    {
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
            return [];
        }

        $contextDefinition = $this->entityManager->find(ContextDefinition::class, $data['contextDefinition']['id']);
        if (!$contextDefinition instanceof ContextDefinition) {
            return [];
        }

        $connectorContextItem->setObjectId($data['objectId']);
        $connectorContextItem->setConnectorEngine($connectorEngine);
        $connectorContextItem->setContextDefinition($contextDefinition);

        return $connectorContextItem;
    }
}
