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

namespace Pimcore\Model\Schedule\Task;

use Pimcore\Model;

/**
 * @internal
 *
 * @method \Pimcore\Model\Schedule\Task\Listing\Dao getDao()
 * @method Model\Schedule\Task[] load()
 * @method Model\Schedule\Task|false current()
 */
class Listing extends Model\Listing\AbstractListing
{
    /**
     * @return Model\Schedule\Task[]
     */
    public function getTasks(): array
    {
        return $this->getData();
    }

    /**
     * @param Model\Schedule\Task[]|null $tasks
     *
     * @return $this
     */
    public function setTasks(?array $tasks): static
    {
        return $this->setData($tasks);
    }
}
