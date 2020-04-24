<?php

namespace JobsBundle\Connector\Google;

use JobsBundle\Context\ResolvedItemInterface;
use JobsBundle\Feed\FeedGeneratorInterface;
use JobsBundle\Transformer\ItemTransformerInterface;

class FeedGenerator implements FeedGeneratorInterface
{
    /**
     * @var ItemTransformerInterface
     */
    protected $itemTransformer;

    /**
     * @var array|ResolvedItemInterface[]
     */
    protected $items;

    /**
     * @var array
     */
    protected $params;

    /**
     * @param ItemTransformerInterface      $itemTransformer
     * @param array|ResolvedItemInterface[] $items
     * @param array                         $params
     */
    public function __construct(ItemTransformerInterface $itemTransformer, array $items, array $params)
    {
        $this->itemTransformer = $itemTransformer;
        $this->items = $items;
        $this->params = $params;
    }

    /**
     * {@inheritdoc}
     */
    public function generate(string $outputType)
    {
        foreach ($this->items as $item) {
            $definition = $this->generateItemTransformerDefinitionClass();
            $definition->setGraph($this->params['graph']);
            $this->itemTransformer->transform($item, $definition);
        }
    }

    /**
     * @return ItemTransformerDefinition
     */
    public function generateItemTransformerDefinitionClass()
    {
        return new ItemTransformerDefinition();
    }
}
