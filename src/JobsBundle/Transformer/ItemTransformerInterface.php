<?php

namespace JobsBundle\Transformer;

use JobsBundle\Context\ResolvedItemInterface;

interface ItemTransformerInterface
{
    /**
     * @param ResolvedItemInterface              $item
     * @param ItemTransformerDefinitionInterface $itemTransformerDefinition
     */
    public function transform(ResolvedItemInterface $item, ItemTransformerDefinitionInterface $itemTransformerDefinition);
}