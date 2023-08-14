<?php

namespace JobsBundle\Context;

use JobsBundle\Model\ConnectorContextItemInterface;
use Pimcore\Model\DataObject\Concrete;

interface ResolvedItemInterface
{
    public function getContextItem(): ?ConnectorContextItemInterface;

    public function getSubject(): ?Concrete;

    public function getResolvedParams(): array;

    public function getResolvedParam(string $param): mixed;
}
