<?php

namespace DachcomBundle\Test\Services;

use JobsBundle\Context\ResolvedItemInterface;
use JobsBundle\Transformer\ItemTransformerDefinitionInterface;
use JobsBundle\Transformer\ItemTransformerInterface;

class TestItemTransformer implements ItemTransformerInterface
{
    public function transform(ResolvedItemInterface $item, ItemTransformerDefinitionInterface $itemTransformerDefinition): void
    {
        // nothing to do so far
    }
}