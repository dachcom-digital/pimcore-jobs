<?php

namespace JobsBundle\Connector\Google;

use JobsBundle\Feed\FeedGeneratorInterface;
use JobsBundle\Transformer\ItemTransformerDefinitionInterface;
use JobsBundle\Transformer\ItemTransformerInterface;

class FeedGenerator implements FeedGeneratorInterface
{

    public function __construct(
        protected ItemTransformerInterface $itemTransformer,
        protected array $items,
        protected array $params
    )
    {
    }

    public function generate(string $outputType): mixed
    {
        foreach ($this->items as $item) {
            $definition = $this->generateItemTransformerDefinitionClass();
            if ($definition instanceof ItemTransformerDefinition) {
                $definition->setGraph($this->params['graph']);
            }
            $this->itemTransformer->transform($item, $definition);
        }

        return null;
    }

    public function generateItemTransformerDefinitionClass(): ItemTransformerDefinitionInterface
    {
        return new ItemTransformerDefinition();
    }
}
