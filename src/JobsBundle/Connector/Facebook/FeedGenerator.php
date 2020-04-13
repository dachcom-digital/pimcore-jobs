<?php

namespace JobsBundle\Connector\Facebook;

use Carbon\Carbon;
use JobsBundle\Context\ResolvedItemInterface;
use JobsBundle\Feed\FeedGeneratorInterface;
use JobsBundle\Transformer\ItemTransformerInterface;
use Symfony\Component\Serializer\Encoder\XmlEncoder;

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
     * {@inheritDoc}
     */
    public function generate(string $outputType)
    {
        $feed = $this->generateFeedTransformerDefinitionClass();

        $feed->setLastBuildDate(new Carbon());
        $feed->setPublisherName($this->params['publisherName']);
        $feed->setPublisherUrl($this->params['publisherUrl']);

        foreach ($this->items as $item) {
            $definition = $this->generateItemTransformerDefinitionClass();
            $this->itemTransformer->transform($item, $definition);
            $feed->addItem($definition);
        }

        if ($outputType === 'xml') {
            $encoder = new XmlEncoder('source');
            return $encoder->encode($feed->toArray(), 'xml');
        }

        return $feed->toArray();
    }

    /**
     * {@inheritDoc}
     */
    public function generateItemTransformerDefinitionClass()
    {
        return new ItemTransformerDefinition();
    }

    /**
     * {@inheritDoc}
     */
    protected function generateFeedTransformerDefinitionClass()
    {
        return new FeedTransformerDefinition();
    }
}
