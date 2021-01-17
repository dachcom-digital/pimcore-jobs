<?php

namespace DachcomBundle\Test\functional\Frontend\Navigation;

use DachcomBundle\Test\FunctionalTester;

class ObjectConnectorCest
{
    /**
     * @param FunctionalTester $I
     */
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
            'host'   => 'https:/www.example.com'
        ]);

        $I->haveAObjectWithConnector($object, $connector, $contextDefinition);

        $newObject = $I->copyObject($object, $folder2);
        $I->seeObjectWithConnectorAndActiveDefinitions($newObject, [$contextDefinition->getId()]);
    }
}