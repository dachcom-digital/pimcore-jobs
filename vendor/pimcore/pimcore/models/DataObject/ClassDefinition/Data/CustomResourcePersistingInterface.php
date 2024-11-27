<?php
declare(strict_types=1);

/**
 * Pimcore
 *
 * This source file is available under two different licenses:
 * - GNU General Public License version 3 (GPLv3)
 * - Pimcore Commercial License (PCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 *  @copyright  Copyright (c) Pimcore GmbH (http://www.pimcore.org)
 *  @license    http://www.pimcore.org/license     GPLv3 and PCL
 */

namespace Pimcore\Model\DataObject\ClassDefinition\Data;

use Pimcore\Model\DataObject\Concrete;
use Pimcore\Model\DataObject\Localizedfield;
use Pimcore\Model\DataObject\Objectbrick\Data\AbstractData;

interface CustomResourcePersistingInterface
{
    public function save(Localizedfield|\Pimcore\Model\DataObject\Fieldcollection\Data\AbstractData|AbstractData|Concrete $object, array $params = []): void;

    public function load(Localizedfield|\Pimcore\Model\DataObject\Fieldcollection\Data\AbstractData|AbstractData|Concrete $object, array $params = []): mixed;

    public function delete(Localizedfield|\Pimcore\Model\DataObject\Fieldcollection\Data\AbstractData|AbstractData|Concrete $object, array $params = []): void;
}
