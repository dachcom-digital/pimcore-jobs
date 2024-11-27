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

namespace Pimcore\Tests\Support\Helper\DataType;

use Pimcore\Cache\RuntimeCache;
use Pimcore\Model\DataObject\ClassDefinition\CalculatorClassInterface;
use Pimcore\Model\DataObject\Concrete;
use Pimcore\Model\DataObject\Data\CalculatedValue;

class Calculator implements CalculatorClassInterface
{
    public function compute(Concrete $object, CalculatedValue $context): string
    {
        $value = '';
        if (RuntimeCache::isRegistered('modeltest.testCalculatedValue.value')) {
            $value = RuntimeCache::get('modeltest.testCalculatedValue.value');
        }

        return $value;
    }

    public function getCalculatedValueForEditMode(Concrete $object, CalculatedValue $context): string
    {
    }
}
