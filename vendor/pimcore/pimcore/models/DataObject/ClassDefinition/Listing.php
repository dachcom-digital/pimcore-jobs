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

namespace Pimcore\Model\DataObject\ClassDefinition;

use Pimcore\Model;

/**
 * @method \Pimcore\Model\DataObject\ClassDefinition\Listing\Dao getDao()
 * @method Model\DataObject\ClassDefinition[] load()
 * @method Model\DataObject\ClassDefinition|false current()
 */
class Listing extends Model\Listing\AbstractListing
{
    /**
     * @return Model\DataObject\ClassDefinition[]
     */
    public function getClasses(): array
    {
        return $this->getData();
    }

    /**
     * @param Model\DataObject\ClassDefinition[]|null $classes
     *
     * @return $this
     */
    public function setClasses(?array $classes): static
    {
        return $this->setData($classes);
    }
}
