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

namespace Pimcore\Model\User\Permission\Definition;

use Pimcore\Model;

/**
 * @method \Pimcore\Model\User\Permission\Definition\Listing\Dao getDao()
 * @method Model\User\Permission\Definition[] load()
 * @method Model\User\Permission\Definition|false current()
 */
class Listing extends Model\Listing\AbstractListing
{
    /**
     * @param Model\User\Permission\Definition[] $definitions
     *
     * @return $this
     */
    public function setDefinitions(array $definitions): static
    {
        return $this->setData($definitions);
    }

    /**
     * @return Model\User\Permission\Definition[]
     */
    public function getDefinitions(): array
    {
        return $this->getData();
    }
}
