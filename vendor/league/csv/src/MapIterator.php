<?php

/**
 * League.Csv (https://csv.thephpleague.com)
 *
 * (c) Ignace Nyamagana Butera <nyamsprod@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace League\Csv;

use ArrayIterator;
use Iterator;
use IteratorAggregate;
use IteratorIterator;
use Traversable;

/**
 * Maps value from an iterator before yielding.
 *
 * @internal used internally to modify CSV content
 */
final class MapIterator extends IteratorIterator
{
    /** @var callable The callback to apply on all InnerIterator current value. */
    private $callable;

    public function __construct(Traversable $iterator, callable $callable)
    {
        parent::__construct($iterator);
        $this->callable = $callable;
    }

    public static function fromIterable(iterable $iterator, callable $callable): self
    {
        return match (true) {
            $iterator instanceof Traversable => new self($iterator, $callable),
            is_array($iterator) => new self(new ArrayIterator($iterator), $callable),
        };
    }

    public function current(): mixed
    {
        return ($this->callable)(parent::current(), parent::key());
    }

    public static function toIterator(iterable $value): Iterator
    {
        if ($value instanceof IteratorAggregate) {
            $value = $value->getIterator();
        }

        return match (true) {
            $value instanceof Iterator => $value,
            $value instanceof Traversable => (function () use ($value): Iterator {
                foreach ($value as $offset => $record) {
                    yield $offset => $record;
                }
            })(),
            default => new ArrayIterator($value),
        };
    }
}
