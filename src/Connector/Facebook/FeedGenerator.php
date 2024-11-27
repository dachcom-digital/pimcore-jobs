<?php

/*
 * This source file is available under two different licenses:
 *   - GNU General Public License version 3 (GPLv3)
 *   - DACHCOM Commercial License (DCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) DACHCOM.DIGITAL AG (https://www.dachcom-digital.com)
 * @license    GPLv3 and DCL
 */

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
