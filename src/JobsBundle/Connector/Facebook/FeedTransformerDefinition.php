<?php

namespace JobsBundle\Connector\Facebook;

use Carbon\Carbon;

class FeedTransformerDefinition
{
    /**
     * @var string
     */
    protected $publisherName;

    /**
     * @var string
     */
    protected $publisherUrl;

    /**
     * @var Carbon
     */
    protected $lastBuildDate;

    /**
     * @var array|ItemTransformerDefinition[]
     */
    protected $items;

    public function __construct()
    {
        $this->items = [];
    }

    /**
     * @param ItemTransformerDefinition $item
     */
    public function addItem(ItemTransformerDefinition $item)
    {
        $this->items[] = $item;
    }

    /**
     * @return Carbon
     */
    public function getLastBuildDate(): Carbon
    {
        return $this->lastBuildDate;
    }

    /**
     * @param Carbon $lastBuildDate
     */
    public function setLastBuildDate(Carbon $lastBuildDate): void
    {
        $this->lastBuildDate = $lastBuildDate;
    }

    /**
     * @return string
     */
    public function getPublisherUrl(): string
    {
        return $this->publisherUrl;
    }

    /**
     * @param string $publisherUrl
     */
    public function setPublisherUrl(string $publisherUrl): void
    {
        $this->publisherUrl = $publisherUrl;
    }

    /**
     * @return string
     */
    public function getPublisherName(): string
    {
        return $this->publisherName;
    }

    /**
     * @param string $publisherName
     */
    public function setPublisherName(string $publisherName): void
    {
        $this->publisherName = $publisherName;
    }

    /**
     * @return array
     */
    public function toArray()
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
