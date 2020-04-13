<?php

namespace JobsBundle\Service;

use JobsBundle\Model\ConnectorContextItemInterface;
use Pimcore\Model\DataObject\ClassDefinition\LinkGeneratorInterface;
use Pimcore\Model\DataObject\Concrete;

class LinkGeneratorService implements LinkGeneratorServiceInterface
{
    /**
     * {@inheritDoc}
     */
    public function generate(Concrete $object, ConnectorContextItemInterface $contextItem)
    {
        $linkGeneratorContext = [
            'contextName' => 'jobs.link_generator',
            'connectorContextItem' => $contextItem
        ];

        $dataUrl = null;
        $linkGenerator = $object->getClass()->getLinkGenerator();
        if ($linkGenerator instanceof LinkGeneratorInterface) {
            $dataUrl = $linkGenerator->generate($object, $linkGeneratorContext);
        }

        return $dataUrl;
    }
}