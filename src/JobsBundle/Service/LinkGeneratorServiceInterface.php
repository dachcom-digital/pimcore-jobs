<?php

namespace JobsBundle\Service;

use JobsBundle\Model\ConnectorContextItemInterface;
use Pimcore\Model\DataObject\Concrete;

interface LinkGeneratorServiceInterface
{
    public function generate(Concrete $object, ConnectorContextItemInterface $contextItem): ?string;
}
