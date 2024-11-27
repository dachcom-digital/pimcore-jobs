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

namespace JobsBundle\EventListener\Admin;

use Pimcore\Event\BundleManager\PathsEvent;
use Pimcore\Event\BundleManagerEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class AssetListener implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            BundleManagerEvents::CSS_PATHS          => 'addCssFiles',
            BundleManagerEvents::JS_PATHS           => 'addJsFiles',
        ];
    }

    public function addCssFiles(PathsEvent $event): void
    {
        $event->addPaths([
            '/bundles/jobs/css/admin.css'
        ]);
    }

    public function addJsFiles(PathsEvent $event): void
    {
        $event->addPaths([
            '/bundles/jobs/js/plugin.js',
            '/bundles/jobs/js/settingsPanel.js',
            '/bundles/jobs/js/connector/abstractConnector.js',
            '/bundles/jobs/js/connector/google.js',
            '/bundles/jobs/js/connector/facebook.js',
            '/bundles/jobs/js/coreExtension/data/jobConnectorContext.js',
            '/bundles/jobs/js/coreExtension/tags/jobConnectorContext.js',
        ]);
    }
}
