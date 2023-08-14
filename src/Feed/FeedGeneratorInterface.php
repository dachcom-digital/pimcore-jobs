<?php

namespace JobsBundle\Feed;

use JobsBundle\Transformer\ItemTransformerDefinitionInterface;

interface FeedGeneratorInterface
{
    public function generate(string $outputType): mixed;

    public function generateItemTransformerDefinitionClass(): ItemTransformerDefinitionInterface;
}
