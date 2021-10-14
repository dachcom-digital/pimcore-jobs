<?php

namespace JobsBundle\Model;

interface ContextDefinitionInterface
{
    public function getId(): ?int;

    public function setHost(string $host): void;

    public function getHost(): string;

    public function setLocale(string $locale): void;

    public function getLocale(): string;

    public function isFromClone(): bool;
}
