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
use Pimcore\Model\Element\ValidationException;
use Symfony\Component\Serializer\Serializer;

class JobConnectorContext extends Data implements
    Data\CustomResourcePersistingInterface,
    Data\CustomVersionMarshalInterface,
    Data\CustomRecyclingMarshalInterface
{
    /**
     * Static type of this element.
     *
     * @var string
     */
    public $fieldtype = 'jobConnectorContext';

    /**
     * @var int
     */
    public $height;

    /**
     * @return ConnectorContextManagerInterface
     */
    private function getConnectorContextManager()
    {
        return \Pimcore::getContainer()->get(ConnectorContextManager::class);
    }

    /**
     * @return ConnectorManagerInterface
     */
    private function getConnectorManager()
    {
        return \Pimcore::getContainer()->get(ConnectorManager::class);
    }

    /**
     * @return LogManagerInterface
     */
    private function getLogManager()
    {
        return \Pimcore::getContainer()->get(LogManager::class);
    }

    /**
     * @return Serializer
     */
    protected function getSerializer()
    {
        return \Pimcore::getContainer()->get('serializer');
    }

    /**
     * @param mixed $object
     *
     * @return ConnectorContextItemInterface[]
     */
    public function preGetData($object)
    {
        if (!$object instanceof Concrete) {
            return null;
        }

        $data = $object->getObjectVar($this->getName());

        if (!$object->isLazyKeyLoaded($this->getName())) {
            $data = $this->load($object, ['force' => true]);
            $setter = 'set' . ucfirst($this->getName());
            if (method_exists($object, $setter)) {
                $object->$setter($data);
            }
        }

        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function preSetData($object, $data, $params = [])
    {
        $this->markAsLoaded($object);

        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function isDiffChangeAllowed($object, $params = [])
    {
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function getDiffDataForEditMode($data, $object = null, $params = [])
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function getDataFromResource($data, $object = null, $params = [])
    {
        return [];
    }

    /**
     * @param mixed $data
     * @param null  $object
     * @param array $params
     *
     * @return ConnectorContextItemInterface[]
     */
    public function getDataForEditmode($data, $object = null, $params = [])
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
     * @param mixed $data
     * @param null  $object
     * @param array $params
     *
     * @return array
     *
     * @throws \Exception
     */
    public function getDataFromEditmode($data, $object = null, $params = [])
    {
        if (!is_array($data)) {
            return null;
        }

        if (!method_exists($object, 'getJobConnectorContext')) {
            return null;
        }

        if (!$object instanceof Concrete) {
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

                $item = array_reduce($existingConnectorContextItems, function ($result, ConnectorContextItemInterface $item) use ($contextConfig) {
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

    /**
     * @param mixed $object
     * @param array $params
     */
    public function save($object, $params = [])
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

            // pimcore's deepcopy does not support doctrine entities in a good way so any entity and subentity gets simply cloned
            // since we don't want to clone our manyToOne relations, we need to re-assign them
            // @todo: remove isFromClone with pimcore >= 7.2 and use CustomDataCopyInterface interface instead!

            if ($connectorContextItem->getConnectorEngine()->isFromClone()) {
                $connectorContextItem->setConnectorEngine($this->getConnectorManager()->getEngineById($connectorContextItem->getConnectorEngine()->getId()));
            }

            if ($connectorContextItem->getContextDefinition()->isFromClone()) {
                $connectorContextItem->setContextDefinition($this->getConnectorContextManager()->getContextDefinition($connectorContextItem->getContextDefinition()->getId()));
            }

            $this->getConnectorContextManager()->update($connectorContextItem);

            if ($connectorContextItem->getId()) {
                $validConnectorContextItems[] = $connectorContextItem->getId();
            }
        }

        foreach ($availableConnectorContextItems as $availableConnectorContextItem) {
            if (!in_array($availableConnectorContextItem->getId(), $validConnectorContextItems)) {
                $this->getConnectorContextManager()->delete($availableConnectorContextItem);
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function load($object, $params = [])
    {
        if (isset($params['force']) && $params['force']) {
            return $this->getConnectorContextManager()->getForObject($object->getId());
        }

        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function delete($object, $params = [])
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

    /**
     * @param mixed $data
     * @param null  $relatedObject
     * @param mixed $params
     * @param null  $idMapper
     *
     * @return ConnectorContextItemInterface[]
     *
     * @throws \Exception
     */
    public function getFromWebserviceImport($data, $relatedObject = null, $params = [], $idMapper = null)
    {
        return $this->getDataFromEditmode($this->arrayCastRecursive($data), $relatedObject, $params);
    }

    /**
     * @param \stdClass[] $array
     *
     * @return array
     */
    protected function arrayCastRecursive($array)
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
            return $this->arrayCastRecursive((array) $array);
        }

        return $array;
    }

    /**
     * @param Concrete $object
     */
    protected function markAsLoaded($object)
    {
        if (!$object instanceof Concrete) {
            return;
        }

        $object->markLazyKeyAsLoaded($this->getName());
    }

    /**
     * {@inheritdoc}
     */
    public function marshalVersion($object, $data)
    {
        if (!is_array($data)) {
            return [];
        }

        return $this->getSerializer()->normalize($data, 'array', ['groups' => ['Version']]);
    }

    /**
     * {@inheritdoc}
     */
    public function unmarshalVersion($object, $data)
    {
        if (!is_array($data)) {
            return [];
        }

        return array_filter(
            $this->getSerializer()->denormalize($data, sprintf('%s[]', ConnectorContextItem::class))
        );
    }

    /**
     * {@inheritdoc}
     */
    public function marshalRecycleData($object, $data)
    {
        if (!is_array($data)) {
            return [];
        }

        return $this->getSerializer()->normalize($data, 'array', ['groups' => ['Version']]);
    }

    /**
     * {@inheritdoc}
     */
    public function unmarshalRecycleData($object, $data)
    {
        if (!is_array($data)) {
            return [];
        }

        return array_filter(
            $this->getSerializer()->denormalize($data, sprintf('%s[]', ConnectorContextItem::class))
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getVersionPreview($data, $object = null, $params = [])
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

        return join(', ', $preview);
    }

    /**
     * {@inheritdoc}
     */
    public function getDataForSearchIndex($object, $params = [])
    {
        return '';
    }

}
