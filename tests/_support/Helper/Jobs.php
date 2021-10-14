<?php

namespace DachcomBundle\Test\Helper;

use Codeception\Module;
use Codeception\TestInterface;
use Dachcom\Codeception\Helper\PimcoreCore;
use JobsBundle\Connector\ConnectorServiceInterface;
use JobsBundle\Manager\ConnectorContextManagerInterface;
use JobsBundle\Manager\ContextDefinitionManagerInterface;
use JobsBundle\Model\ConnectorContextItemInterface;
use JobsBundle\Model\ConnectorEngineInterface;
use JobsBundle\Model\ContextDefinitionInterface;
use Pimcore\Db;
use Pimcore\Model\DataObject;
use Symfony\Component\DependencyInjection\Container;

class Jobs extends Module
{
    public function _after(TestInterface $test): void
    {
        parent::_after($test);

        $db = Db::get();
        $db->exec('SET FOREIGN_KEY_CHECKS = 0;');
        $db->exec('TRUNCATE TABLE jobs_connector_context_item;');
        $db->exec('TRUNCATE TABLE jobs_connector_engine;');
        $db->exec('TRUNCATE TABLE jobs_context_definition;');
        $db->exec('TRUNCATE TABLE jobs_log;');
        $db->exec('SET FOREIGN_KEY_CHECKS = 1;');
    }

    public function haveAJobConnector(array $params): ConnectorEngineInterface
    {
        $connectorService = $this->getContainer()->get(ConnectorServiceInterface::class);

        $connectorEngine = $connectorService->installConnector($params['name']);

        $connectorService->enableConnector($params['name']);
        $connectorService->connectConnector($params['name']);

        $this->assertInstanceOf(ConnectorEngineInterface::class, $connectorEngine);

        return $connectorEngine;
    }

    public function haveContextDefinition(array $params): ContextDefinitionInterface
    {
        $contextDefinitionManager = $this->getContainer()->get(ContextDefinitionManagerInterface::class);

        $contextDefinition = $contextDefinitionManager->createNew($params['host'], $params['locale']);

        $this->assertInstanceOf(ContextDefinitionInterface::class, $contextDefinition);

        return $contextDefinition;
    }

    public function haveAObjectWithConnector(DataObject $object, ConnectorEngineInterface $connector, array $contextDefinitions): DataObject
    {
        $connectorContextManager = $this->getContainer()->get(ConnectorContextManagerInterface::class);

        $items = [];
        foreach ($contextDefinitions as $contextDefinition) {
            $item = $connectorContextManager->createNew($connector->getId());
            $item->setObjectId($object->getId());
            $item->setContextDefinition($contextDefinition);

            $this->assertInstanceOf(ConnectorContextItemInterface::class, $item);

            $items[] = $item;
        }

        $object->setJobConnectorContext($items);
        $object->save();

        $this->assertTrue(is_array($object->getJobConnectorContext()));

        return $object;
    }

    public function addContextDefinitionToObjectConnectorWithoutSaving(DataObject $object, ConnectorEngineInterface $connector, array $contextDefinitions): DataObject
    {
        $connectorContextManager = $this->getContainer()->get(ConnectorContextManagerInterface::class);

        $items = $object->getJobConnectorContext();
        if (!is_array($items)) {
            $items = [];
        }

        foreach ($contextDefinitions as $contextDefinition) {
            $item = $connectorContextManager->createNew($connector->getId());
            $item->setObjectId($object->getId());
            $item->setContextDefinition($contextDefinition);

            $this->assertInstanceOf(ConnectorContextItemInterface::class, $item);

            $items[] = $item;
        }

        $object->setJobConnectorContext($items);

        $this->assertTrue(is_array($object->getJobConnectorContext()));

        return $object;
    }

    public function removeAllContextDefinitionFromObjectConnectorWithoutSaving(DataObject $object): DataObject
    {
        $object->setJobConnectorContext(null);

        $this->assertTrue(is_null($object->getJobConnectorContext()));

        return $object;
    }

    public function seeObjectWithActiveContextDefinitions(DataObject $object, array $activeContextDefinitions): void
    {
        $context = $object->getJobConnectorContext();

        $this->assertTrue(is_array($context));

        /** @var ConnectorContextItemInterface $item */
        foreach ($context as $item) {
            $this->assertContains($item->getContextDefinition()->getId(), $activeContextDefinitions);
        }
    }

    public function seeObjectWithNoContextDefinitions(DataObject $object, array $activeContextDefinitions): void
    {
        $context = $object->getJobConnectorContext();

        $this->assertCount(0, $context);
    }

    protected function getContainer(): Container
    {
        return $this->getModule('\\' . PimcoreCore::class)->getContainer();
    }
}
