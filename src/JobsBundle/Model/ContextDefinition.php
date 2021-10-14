<?php

namespace JobsBundle\Model;

class ContextDefinition implements ContextDefinitionInterface
{
    protected ?int $id = null;
    protected string $host;
    protected string $locale;
    protected bool $fromClone = false;

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setHost(string $host): void
    {
        $this->host = $host;
    }

    public function getHost(): string
    {
        return $this->host;
    }

    public function setLocale(string $locale): void
    {
        $this->locale = $locale;
    }

    public function getLocale(): string
    {
        return $this->locale;
    }

    public function isFromClone(): bool
    {
        return $this->fromClone;
    }

    public function __clone()
    {
        $this->fromClone = true;
    }
}
