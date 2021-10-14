<?php

namespace DachcomBundle\Test\functional\Frontend\Navigation;

use DachcomBundle\Test\FunctionalTester;

class ObjectConnectorCest
{
    public function testObjectCopyWithConnector(FunctionalTester $I)
    {
        $classDefinition = $I->haveAPimcoreClass('TestClass');

        $folder1 = $I->haveAPimcoreObjectFolder('folder-1');
        $folder2 = $I->haveAPimcoreObjectFolder('folder-2');

        $object = $I->haveASubPimcoreObject($folder1, $classDefinition->getName(), 'object-1', ['name' => 'test']);

        $connector = $I->haveAJobConnector([
            'name'   => 'test',
            'config' => []
        ]);

        $contextDefinition = $I->haveContextDefinition([
            'locale' => 'en',
            'host'   => 'https://www.example.com'
        ]);

        $I->haveAObjectWithConnector($object, $connector, [$contextDefinition]);

        $newObject = $I->copyObject($object, $folder2);
        $I->seeObjectWithActiveContextDefinitions($newObject, [$contextDefinition->getId()]);
    }

    public function testObjectVersionWithNoDefinitionInConnector(FunctionalTester $I)
    {
        $classDefinition = $I->haveAPimcoreClass('TestClass');

        $folder1 = $I->haveAPimcoreObjectFolder('folder-1');

        $object = $I->haveASubPimcoreObject($folder1, $classDefinition->getName(), 'object-1', ['name' => 'test']);

        $connector = $I->haveAJobConnector([
            'name'   => 'test',
            'config' => []
        ]);

        $contextDefinition = $I->haveContextDefinition([
            'locale' => 'en',
            'host'   => 'https://www.example.com'
        ]);

        $I->haveAObjectWithConnector($object, $connector, [$contextDefinition]);

        $object = $I->removeAllContextDefinitionFromObjectConnectorWithoutSaving($object);
        $newVersion = $I->createNewObjectVersion($object);

        $object = $I->refreshObject($object);
        $I->seeObjectWithActiveContextDefinitions($object, [$contextDefinition->getId()]);

        $object = $I->publishObjectVersion($newVersion);
        $object = $I->refreshObject($object);

        $I->seeObjectWithActiveContextDefinitions($object, [$contextDefinition->getId()]);
    }

    public function testObjectVersionWithAdditionalDefinitionInConnector(FunctionalTester $I)
    {
        $classDefinition = $I->haveAPimcoreClass('TestClass');

        $folder1 = $I->haveAPimcoreObjectFolder('folder-1');

        $object = $I->haveASubPimcoreObject($folder1, $classDefinition->getName(), 'object-1', ['name' => 'test']);

        $connector = $I->haveAJobConnector([
            'name'   => 'test',
            'config' => []
        ]);

        $contextDefinition1 = $I->haveContextDefinition([
            'locale' => 'en',
            'host'   => 'https://www.example.com'
        ]);

        $contextDefinition2 = $I->haveContextDefinition([
            'locale' => 'de',
            'host'   => 'https://www.example.de'
        ]);

        $I->haveAObjectWithConnector($object, $connector, [$contextDefinition1]);

        $object = $I->addContextDefinitionToObjectConnectorWithoutSaving($object, $connector, [$contextDefinition2]);
        $newVersion = $I->createNewObjectVersion($object);

        $object = $I->refreshObject($object);
        $I->seeObjectWithActiveContextDefinitions($object, [$contextDefinition1->getId()]);

        $object = $I->publishObjectVersion($newVersion);
        $object = $I->refreshObject($object);

        $I->seeObjectWithActiveContextDefinitions($object, [$contextDefinition1->getId(), $contextDefinition2->getId()]);
    }

    public function testObjectRecycleBinWithConnector(FunctionalTester $I)
    {
        $classDefinition = $I->haveAPimcoreClass('TestClass');

        $folder1 = $I->haveAPimcoreObjectFolder('folder-1');

        $object = $I->haveASubPimcoreObject($folder1, $classDefinition->getName(), 'object-1', ['name' => 'test']);

        $connector = $I->haveAJobConnector([
            'name'   => 'test',
            'config' => []
        ]);

        $contextDefinition = $I->haveContextDefinition([
            'locale' => 'en',
            'host'   => 'https://www.example.com'
        ]);

        $I->haveAObjectWithConnector($object, $connector, [$contextDefinition]);

        $recycleBinItem = $I->moveObjectToRecycleBin($object);
        $restoredObject = $I->restoreObjectFromRecycleBin($object, $recycleBinItem);

        $I->seeObjectWithActiveContextDefinitions($restoredObject, [$contextDefinition->getId()]);
    }
}