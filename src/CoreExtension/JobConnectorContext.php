<?php

namespace JobsBundle\CoreExtension;

use JobsBundle\Manager\ConnectorContextManager;
use JobsBundle\Manager\ConnectorContextManagerInterface;
use JobsBundle\Manager\ConnectorManager;
use JobsBundle\Manager\ConnectorManagerInterface;
use JobsBundle\Manager\LogManager;
use JobsBundle\Manager\LogManagerInterface;
use JobsBundle\Model\ConnectorContextItem;
use JobsBundle\Model\ConnectorContextItemInterface;
use JobsBundle\Model\ConnectorEngineInterface;
use JobsBundle\Model\ContextDefinitionInterface;
use Pimcore\Model\DataObject\ClassDefinition\Data;
use Pimcore\Model\DataObject\Concrete;
use Pimcore\Model\DataObject\Fieldcollection;
use Pimcore\Model\DataObject\Localizedfield;
use Pimcore\Model\DataObject\Objectbrick;
use Pimcore\Model\DataObject\Objectbrick\Data\AbstractData;
use Pimcore\Model\Element\ValidationException;
use Symfony\Component\Serializer\Serializer;

class JobConnectorContext extends Data implements
    Data\CustomResourcePersistingInterface,
    Data\CustomVersionMarshalInterface,
    Data\CustomRecyclingMarshalInterface,
    Data\CustomDataCopyInterface,
    Data\PreGetDataInterface,
    Data\PreSetDataInterface
{
    private function getConnectorContextManager(): ConnectorContextManagerInterface
    {
        return \Pimcore::getContainer()->get(ConnectorContextManager::class);
    }

    private function getConnectorManager(): ConnectorManagerInterface
    {
        return \Pimcore::getContainer()->get(ConnectorManager::class);
    }

    private function getLogManager(): LogManagerInterface
    {
        return \Pimcore::getContainer()->get(LogManager::class);
    }

    protected function getSerializer(): Serializer
    {
        return \Pimcore::getContainer()->get('jobs.internal.serializer');
    }

    public function preGetData(mixed $container, array $params = []): mixed
    {
        if (!$container instanceof Concrete) {
            return null;
        }

        $data = $container->getObjectVar($this->getName());

        if (!$container->isLazyKeyLoaded($this->getName())) {
            $data = $this->load($container, ['force' => true]);
            $setter = 'set' . ucfirst($this->getName());
            if (method_exists($container, $setter)) {
                $container->$setter($data);
            }
        }

        return $data;
    }

    public function preSetData(mixed $container, mixed $data, array $params = []): mixed
    {
        $this->markAsLoaded($container);

        return $data;
    }

    public function getDiffDataForEditMode(mixed $data, Concrete $object = null, array $params = []): ?array
    {
        return [];
    }

    /**
     * @return ConnectorContextItemInterface[]
     */
    public function getDataForEditmode(mixed $data, Concrete $object = null, array $params = []): mixed
    {
        if (!$object instanceof Concrete) {
            return $data;
        }

        if (!is_array($data)) {
            $data = [];
        }

        return $this->getConnectorContextManager()->generateConnectorContextConfig($data);
    }

    /**
     * @throws \Exception
     */
    public function getDataFromEditmode(mixed $data, Concrete $object = null, array $params = []): mixed
    {
        if (!is_array($data)) {
            return null;
        }

        if (!method_exists($object, 'getJobConnectorContext')) {
            return null;
        }

        $items = [];
        foreach ($data as $connectorContext) {
            $connectorId = $connectorContext['connectorId'];
            $connectorName = $connectorContext['connectorName'];
            $contextItems = $connectorContext['contextItems'];

            if (!is_numeric($connectorId)) {
                continue;
            }

            $existingConnectorContextItems = $this->getConnectorContextManager()->getForConnectorEngineAndObject((int) $connectorId, $object->getId());
            $multipleContextItemsAllowed = $this->getConnectorContextManager()->connectorAllowsMultipleContextItems($connectorName);

            if (count($contextItems) > 1 && $multipleContextItemsAllowed === false) {
                throw new ValidationException(sprintf('Invalid Job context configuration. "%s" Connector does not allow multiple items.', ucfirst($connectorName)));
            }

            foreach ($contextItems as $contextConfig) {

                if ($contextConfig['active'] === false) {
                    continue;
                }

                $item = array_reduce($existingConnectorContextItems, static function ($result, ConnectorContextItemInterface $item) use ($contextConfig) {
                    return $item->getContextDefinition()->getId() === $contextConfig['id'] ? $item : $result;
                });

                if (!$item instanceof ConnectorContextItemInterface) {
                    $item = $this->getConnectorContextManager()->createNew($connectorId);
                }

                $item->setObjectId($object->getId());
                $item->setContextDefinition($this->getConnectorContextManager()->getContextDefinition($contextConfig['id']));

                $items[] = $item;
            }
        }

        return $items;
    }

    public function save(Localizedfield|\Pimcore\Model\DataObject\Fieldcollection\Data\AbstractData|AbstractData|Concrete $object, array $params = []): void
    {
        if (!method_exists($object, 'getJobConnectorContext')) {
            return;
        }

        if (!$object instanceof Concrete) {
            return;
        }

        $jobConnectorContext = $object->getObjectVar($this->getName());

        if (!is_array($jobConnectorContext)) {
            return;
        }

        $validConnectorContextItems = [];
        $availableConnectorContextItems = $this->load($object, ['force' => true]);

        /** @var ConnectorContextItemInterface $connectorContextItem */
        foreach ($jobConnectorContext as $connectorContextItem) {
            $connectorContextItem->setObjectId($object->getId());

            $this->getConnectorContextManager()->update($connectorContextItem);

            if ($connectorContextItem->getId()) {
                $validConnectorContextItems[] = $connectorContextItem->getId();
            }
        }

        foreach ($availableConnectorContextItems as $availableConnectorContextItem) {
            if (!in_array($availableConnectorContextItem->getId(), $validConnectorContextItems, true)) {
                $this->getConnectorContextManager()->delete($availableConnectorContextItem);
            }
        }
    }

    public function load(Localizedfield|\Pimcore\Model\DataObject\Fieldcollection\Data\AbstractData|AbstractData|Concrete $object, array $params = []): mixed
    {
        if (isset($params['force']) && $params['force']) {
            return $this->getConnectorContextManager()->getForObject($object->getId());
        }

        return null;
    }

    public function delete(Localizedfield|\Pimcore\Model\DataObject\Fieldcollection\Data\AbstractData|AbstractData|Concrete $object, array $params = []): void
    {
        $allConnectorContextItems = $this->load($object, ['force' => true]);
        if (!is_array($allConnectorContextItems)) {
            return;
        }

        foreach ($allConnectorContextItems as $connectorContextItem) {
            $this->getConnectorContextManager()->delete($connectorContextItem);
        }

        $this->getLogManager()->deleteForObject($object->getId());
    }

    protected function arrayCastRecursive(mixed $array): array
    {
        if (is_array($array)) {
            foreach ($array as $key => $value) {
                if (is_array($value)) {
                    $array[$key] = $this->arrayCastRecursive($value);
                }
                if ($value instanceof \stdClass) {
                    $array[$key] = $this->arrayCastRecursive((array) $value);
                }
            }
        }

        if ($array instanceof \stdClass) {
            return $this->arrayCastRecursive([$array]);
        }

        return $array;
    }

    protected function markAsLoaded($object): void
    {
        if (!$object instanceof Concrete) {
            return;
        }

        $object->markLazyKeyAsLoaded($this->getName());
    }

    public function marshalVersion(Concrete $object, mixed $data): mixed
    {
        if (!is_array($data)) {
            return [];
        }

        return $this->getSerializer()->normalize($data, 'array', ['groups' => ['Version']]);
    }

    public function unmarshalVersion(Concrete $object, mixed $data): mixed
    {
        if (!is_array($data)) {
            return [];
        }

        return array_filter(
            $this->getSerializer()->denormalize($data, sprintf('%s[]', ConnectorContextItem::class))
        );
    }

    public function marshalRecycleData(Concrete $object, mixed $data): mixed
    {
        return $this->marshalVersion($object, $data);
    }

    public function unmarshalRecycleData(Concrete $object, mixed $data): mixed
    {
        return $this->unmarshalVersion($object, $data);
    }

    public function createDataCopy(Concrete $object, mixed $data): mixed
    {
        if (!is_array($data)) {
            return [];
        }

        $newData = [];
        /** @var ConnectorContextItemInterface $connectorContextItem */
        foreach ($data as $connectorContextItem) {

            $newConnectorContextItem = clone $connectorContextItem;

            $newConnectorContextItem->setId(null);
            $newConnectorContextItem->setObjectId(null);
            $newConnectorContextItem->setConnectorEngine($this->getConnectorManager()->getEngineById($connectorContextItem->getConnectorEngine()->getId()));
            $newConnectorContextItem->setContextDefinition($this->getConnectorContextManager()->getContextDefinition($connectorContextItem->getContextDefinition()->getId()));

            $newData[] = $newConnectorContextItem;
        }

        return $newData;
    }

    public function getVersionPreview(mixed $data, Concrete $object = null, array $params = []): string
    {
        $preview = [];
        if (!is_array($data)) {
            return $data;
        }

        /** @var ConnectorContextItemInterface $element */
        foreach ($data as $element) {
            $connector = $element->getConnectorEngine() instanceof ConnectorEngineInterface ? $element->getConnectorEngine()->getName() : '[removed]';
            $contextDefinitionId = $element->getContextDefinition() instanceof ContextDefinitionInterface ? $element->getContextDefinition()->getId() : '[removed]';
            $preview[] = (string) sprintf('%s: Context ID %s', $connector, $contextDefinitionId);
        }

        return implode(', ', $preview);
    }

    public function getDataForSearchIndex(Localizedfield|Fieldcollection\Data\AbstractData|Objectbrick\Data\AbstractData|Concrete $object, array $params = []): string
    {
        return '';
    }

    public function getParameterTypeDeclaration(): ?string
    {
        return '?array';
    }

    public function getReturnTypeDeclaration(): ?string
    {
        return $this->getParameterTypeDeclaration();
    }

    public function getPhpdocInputType(): ?string
    {
        return '\\' . ConnectorContextItemInterface::class . '[]';
    }

    public function getPhpdocReturnType(): ?string
    {
        return '\\' . ConnectorContextItemInterface::class . '[]';
    }

    public function getFieldType(): string
    {
        return 'jobConnectorContext';
    }
}
