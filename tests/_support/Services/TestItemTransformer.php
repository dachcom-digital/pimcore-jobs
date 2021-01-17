<?php

namespace DachcomBundle\Test\Services;

use JobsBundle\Context\ResolvedItemInterface;
use JobsBundle\Transformer\ItemTransformerDefinitionInterface;
use JobsBundle\Transformer\ItemTransformerInterface;

class TestItemTransformer implements ItemTransformerInterface
{
    /**
     * {@inheritDoc}
     */
    public function transform(ResolvedItemInterface $item, ItemTransformerDefinitionInterface $itemTransformerDefinition)
    {
    }
}