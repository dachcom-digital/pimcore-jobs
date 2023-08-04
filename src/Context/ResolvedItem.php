<?php

namespace JobsBundle\Context;

use JobsBundle\Model\ConnectorContextItemInterface;
use Pimcore\Model\DataObject\Concrete;

class ResolvedItem implements ResolvedItemInterface
{
    public function __construct(
        protected ?ConnectorContextItemInterface $contextItem,
        protected ?Concrete $subject,
        protected array $resolvedParams = []
    )
    {
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
