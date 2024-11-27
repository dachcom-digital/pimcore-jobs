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

namespace Pimcore\Model\Element\WorkflowState;

use Pimcore\Model;

/**
 * @method \Pimcore\Model\Element\WorkflowState\Listing\Dao getDao()
 * @method Model\Element\WorkflowState[] load()
 * @method Model\Element\WorkflowState|false current()
 */
class Listing extends Model\Listing\AbstractListing
{
    /**
     * @param Model\Element\WorkflowState[]|null $workflowStates
     *
     * @return $this
     */
    public function setWorkflowStates(?array $workflowStates): static
    {
        return $this->setData($workflowStates);
    }

    /**
     * @return Model\Element\WorkflowState[]
     */
    public function getWorkflowStates(): array
    {
        return $this->getData();
    }
}
