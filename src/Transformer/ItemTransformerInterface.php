<?php

namespace JobsBundle\Transformer;

use JobsBundle\Context\ResolvedItemInterface;

interface ItemTransformerInterface
{
    public function transform(ResolvedItemInterface $item, ItemTransformerDefinitionInterface $itemTransformerDefinition): void;
}
