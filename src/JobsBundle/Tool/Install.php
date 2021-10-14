<?php

namespace JobsBundle\Tool;

use Pimcore\Db\Connection;
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
        /** @var Connection $db */
        $db = \Pimcore\Db::get();
        $db->query(file_get_contents($this->getInstallSourcesPath() . '/sql/install.sql'));
    }

    protected function getInstallSourcesPath(): string
    {
        return __DIR__ . '/../Resources/install';
    }
}
