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
    ) {
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
