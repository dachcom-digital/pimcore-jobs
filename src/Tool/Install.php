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

namespace JobsBundle\Tool;

use Pimcore\Extension\Bundle\Installer\SettingsStoreAwareInstaller;

class Install extends SettingsStoreAwareInstaller
{
    public function install(): void
    {
        $this->installDbStructure();

        parent::install();
    }

    protected function installDbStructure(): void
    {
        $db = \Pimcore\Db::get();
        $db->executeQuery(file_get_contents($this->getInstallSourcesPath() . '/sql/install.sql'));
    }

    protected function getInstallSourcesPath(): string
    {
        return __DIR__ . '/../../config/install';
    }
}
