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

namespace JobsBundle\Context;

use JobsBundle\Model\ConnectorContextItemInterface;
use Pimcore\Model\DataObject\Concrete;

class ResolvedItem implements ResolvedItemInterface
{
    public function __construct(
        protected ?ConnectorContextItemInterface $contextItem,
        protected ?Concrete $subject,
        protected array $resolvedParams = []
    ) {
    }

    public function getContextItem(): ?ConnectorContextItemInterface
    {
        return $this->contextItem;
    }

    public function getSubject(): ?Concrete
    {
        return $this->subject;
    }

    public function getResolvedParams(): array
    {
        return $this->resolvedParams;
    }

    public function getResolvedParam(string $param): mixed
    {
        return $this->resolvedParams[$param] ?? null;
    }
}
