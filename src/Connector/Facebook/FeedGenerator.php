<?php

namespace JobsBundle\Connector\Facebook;

use Carbon\Carbon;
use JobsBundle\Feed\FeedGeneratorInterface;
use JobsBundle\Transformer\ItemTransformerDefinitionInterface;
use JobsBundle\Transformer\ItemTransformerInterface;
use Symfony\Component\Serializer\Encoder\XmlEncoder;

class FeedGenerator implements FeedGeneratorInterface
{
    public function __construct(
        protected ItemTransformerInterface $itemTransformer,
        protected array $items,
        protected array $params
    ) {
    }

    public function generate(string $outputType): mixed
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

    public function generateItemTransformerDefinitionClass(): ItemTransformerDefinitionInterface
    {
        return new ItemTransformerDefinition();
    }

    protected function generateFeedTransformerDefinitionClass(): FeedTransformerDefinition
    {
        return new FeedTransformerDefinition();
    }
}
