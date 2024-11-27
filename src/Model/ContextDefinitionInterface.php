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

namespace JobsBundle\Model;

interface ContextDefinitionInterface
{
    public function getId(): ?int;

    public function setHost(string $host): void;

    public function getHost(): string;

    public function setLocale(string $locale): void;

    public function getLocale(): string;
}
