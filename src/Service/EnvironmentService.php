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

namespace JobsBundle\Service;

class EnvironmentService implements EnvironmentServiceInterface
{
    public function __construct(
        protected string $dataClass,
        protected string $feedHost
    ) {
    }

    public function getDataClass(): string
    {
        return $this->dataClass;
    }

    public function getFeedHost(): string
    {
        return $this->feedHost;
    }
}
