<?php

namespace JobsBundle\Service;

use JobsBundle\Model\ConnectorContextItemInterface;
use Pimcore\Model\DataObject\Concrete;

interface LinkGeneratorServiceInterface
{
    /**
     * @param Concrete                      $object
     * @param ConnectorContextItemInterface $contextItem
     *
     * @return string|null
     */
    public function generate(Concrete $object, ConnectorContextItemInterface $contextItem);
}
