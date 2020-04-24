<?php

namespace JobsBundle\Tool;

use JobsBundle\Model\ConnectorEngineInterface;

class FeedIdHelper
{
    /**
     * @var array
     */
    protected $feedIds;

    /**
     * @var string
     */
    protected $connectorToken;

    /**
     * @var string
     */
    protected $connectorDefinitionName;

    /**
     * @param ConnectorEngineInterface $connectorEngine
     */
    public function __construct(ConnectorEngineInterface $connectorEngine)
    {
        $this->connectorToken = $connectorEngine->getToken();
        $this->connectorDefinitionName = $connectorEngine->getName();

        if ($connectorEngine->hasFeedIds()) {
            $feedIds = $connectorEngine->getFeedIds();
        } else {
            $feedIds = [];
        }

        $this->feedIds = $feedIds;
    }

    /**
     * @return int
     */
    public function generateFeedId()
    {
        $latestFeedId = count($this->feedIds) === 0 ? 0 : max(array_column($this->feedIds, 'internalId'));

        $latestFeedId++;

        return $latestFeedId;
    }

    /**
     * @param int   $internalFeedId
     * @param mixed $externalFeedId
     *
     * @throws \Exception
     */
    public function addFeedId(int $internalFeedId, $externalFeedId)
    {
        $feedIndex = array_search($internalFeedId, array_column($this->feedIds, 'internalId'));

        if ($feedIndex !== false) {
            throw new \Exception(sprintf('Feed Id %d already exists.', $internalFeedId));
        }

        $latestValidFeedId = $this->generateFeedId();

        if ($internalFeedId !== $latestValidFeedId) {
            throw new \Exception(sprintf('New Feed Id %d is not valid. Should be %d.', $internalFeedId, $latestValidFeedId));
        }

        $this->feedIds[] = [
            'internalId' => $internalFeedId,
            'externalId' => $externalFeedId
        ];
    }

    /**
     * @param int $internalFeedId
     *
     * @throws \Exception
     */
    public function removeFeedId(int $internalFeedId)
    {
        $feedIndex = array_search($internalFeedId, array_column($this->feedIds, 'internalId'));

        if ($feedIndex === false) {
            throw new \Exception(sprintf('Feed Id %d does not exist.', $internalFeedId));
        }

        unset($this->feedIds[$feedIndex]);

        $this->feedIds = array_values($this->feedIds);
    }

    /**
     * @param int $internalId
     *
     * @return array|null
     */
    public function findFeedId(int $internalId)
    {
        $feedIndex = array_search($internalId, array_column($this->feedIds, 'internalId'));

        if ($feedIndex === false) {
            return null;
        }

        return $this->feedIds[$feedIndex];
    }

    /**
     * @param string $feedHost
     *
     * @return array
     */
    public function generateFeedList(string $feedHost)
    {
        $list = [];
        foreach ($this->feedIds as $feedId) {
            $token = empty($this->connectorToken) ? '[INVALID_TOKEN]' : $this->connectorToken;
            $list[] = [
                'internalId' => $feedId['internalId'],
                'externalId' => $feedId['externalId'],
                'feedUrl'    => sprintf('%s/jobs/%s/%s/feed/%s', $feedHost, $this->connectorDefinitionName, $token, $feedId['internalId']),
            ];
        }

        return $list;
    }

    /**
     * @return array
     */
    public function getAsArray()
    {
        return $this->feedIds;
    }
}
