<?php

namespace JobsBundle\Feed;

use JobsBundle\Transformer\ItemTransformerDefinitionInterface;

interface FeedGeneratorInterface
{
    /**
     * @param string $outputType
     *
     * @return mixed
     */
    public function generate(string $outputType);

    /**
     * @return ItemTransformerDefinitionInterface
     */
    public function generateItemTransformerDefinitionClass();
}

