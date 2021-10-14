<?php

namespace JobsBundle\Connector\Google;

use JobsBundle\Feed\FeedGeneratorInterface;
use JobsBundle\Transformer\ItemTransformerDefinitionInterface;
use JobsBundle\Transformer\ItemTransformerInterface;

class FeedGenerator implements FeedGeneratorInterface
{
    protected ItemTransformerInterface $itemTransformer;
    protected array $items;
    protected array $params;

    public function __construct(ItemTransformerInterface $itemTransformer, array $items, array $params)
    {
        $this->itemTransformer = $itemTransformer;
        $this->items = $items;
        $this->params = $params;
    }

    public function generate(string $outputType): mixed
    {
        foreach ($this->items as $item) {
            $definition = $this->generateItemTransformerDefinitionClass();
            $definition->setGraph($this->params['graph']);
            $this->itemTransformer->transform($item, $definition);
        }

        return null;
    }

    public function generateItemTransformerDefinitionClass(): ItemTransformerDefinitionInterface
    {
        return new ItemTransformerDefinition();
    }
}
