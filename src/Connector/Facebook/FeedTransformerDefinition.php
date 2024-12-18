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

namespace JobsBundle\Connector\Facebook;

use Carbon\Carbon;

class FeedTransformerDefinition
{
    protected string $publisherName;
    protected string $publisherUrl;
    protected Carbon $lastBuildDate;
    protected array $items;

    public function __construct()
    {
        $this->items = [];
    }

    public function addItem(ItemTransformerDefinition $item): void
    {
        $this->items[] = $item;
    }

    public function getLastBuildDate(): Carbon
    {
        return $this->lastBuildDate;
    }

    public function setLastBuildDate(Carbon $lastBuildDate): void
    {
        $this->lastBuildDate = $lastBuildDate;
    }

    public function getPublisherUrl(): string
    {
        return $this->publisherUrl;
    }

    public function setPublisherUrl(string $publisherUrl): void
    {
        $this->publisherUrl = $publisherUrl;
    }

    public function getPublisherName(): string
    {
        return $this->publisherName;
    }

    public function setPublisherName(string $publisherName): void
    {
        $this->publisherName = $publisherName;
    }

    public function toArray(): array
    {
        $jobsArray = [];
        foreach ($this->items as $item) {
            $jobsArray[] = $item->toArray();
        }

        return [
            'publisher-name'  => $this->getPublisherName(),
            'publisher-url'   => $this->getPublisherUrl(),
            'last-build-date' => $this->getLastBuildDate()->format('Y-m-d H:i:s'),
            'job'             => $jobsArray,
        ];
    }
}
