<?php

namespace DachcomBundle\Test\Helper;

use Codeception\Exception\ModuleException;
use Codeception\Module;
use Dachcom\Codeception\Helper\PimcoreCore;
use JobsBundle\Connector\ConnectorServiceInterface;
use JobsBundle\Manager\ConnectorContextManagerInterface;
use JobsBundle\Manager\ContextDefinitionManagerInterface;
use JobsBundle\Model\ConnectorContextItemInterface;
use JobsBundle\Model\ConnectorEngineInterface;
use JobsBundle\Model\ContextDefinitionInterface;
use Pimcore\Model\DataObject;
use Symfony\Component\DependencyInjection\Container;

class Jobs extends Module
{
    /**
     * @param array $params
     *
     * @return ConnectorEngineInterface
     * @throws ModuleException
     */
    public function haveAJobConnector(array $params)
    {
        $connectorService = $this->getContainer()->get(ConnectorServiceInterface::class);

        $connectorEngine = $connectorService->installConnector($params['name']);

        $connectorService->enableConnector($params['name']);
        $connectorService->connectConnector($params['name']);

        $this->assertInstanceOf(ConnectorEngineInterface::class, $connectorEngine);

        return $connectorEngine;
    }

    /**
     * @param array $params
     *
     * @return ContextDefinitionInterface
     * @throws ModuleException
     */
    public function haveContextDefinition(array $params)
    {
        $contextDefinitionManager = $this->getContainer()->get(ContextDefinitionManagerInterface::class);

        $contextDefinition = $contextDefinitionManager->createNew($params['host'], $params['locale']);

        $this->assertInstanceOf(ContextDefinitionInterface::class, $contextDefinition);

        return $contextDefinition;
    }

    /**
     * @param DataObject                 $object
     * @param ConnectorEngineInterface   $connector
     * @param ContextDefinitionInterface $contextDefinition
     *
     * @return DataObject
     * @throws ModuleException
     */
    public function haveAObjectWithConnector(DataObject $object, ConnectorEngineInterface $connector, ContextDefinitionInterface $contextDefinition)
    {
        $connectorContextManager = $this->getContainer()->get(ConnectorContextManagerInterface::class);

        $item = $connectorContextManager->createNew($connector->getId());
        $item->setObjectId($object->getId());
        $item->setContextDefinition($contextDefinition);
        $connectorContextManager->update($item);

        $this->assertInstanceOf(ConnectorContextItemInterface::class, $item);

        $object->setJobConnectorContext([$item]);
        $object->save();

        $this->assertTrue(is_array($object->getJobConnectorContext()));

        return $object;
    }

    /**
     * @param DataObject $object
     * @param array      $activeContextDefinitions
     *
     * @return DataObject
     * @throws ModuleException
     */
    public function seeObjectWithConnectorAndActiveDefinitions(DataObject $object, array $activeContextDefinitions)
    {
        $context = $object->getJobConnectorContext();

        $this->assertTrue(is_array($context));

        /** @var ConnectorContextItemInterface $item */
        foreach ($context as $item) {
            $this->assertContains($item->getContextDefinition()->getId(), $activeContextDefinitions);
        }

        return $object;
    }

    /**
     * @return Container
     * @throws ModuleException
     */
    protected function getContainer()
    {
        return $this->getModule('\\' . PimcoreCore::class)->getContainer();
    }
}
