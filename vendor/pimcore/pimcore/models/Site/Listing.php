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

namespace Pimcore\Model\Site;

use Pimcore\Model;

/**
 * @method \Pimcore\Model\Site\Listing\Dao getDao()
 * @method Model\Site[] load()
 * @method Model\Site|false current()
 */
class Listing extends Model\Listing\AbstractListing
{
    /**
     * @return Model\Site[]
     */
    public function getSites(): array
    {
        return $this->getData();
    }

    /**
     * @param Model\Site[]|null $sites
     *
     * @return $this
     */
    public function setSites(?array $sites): static
    {
        return $this->setData($sites);
    }
}
