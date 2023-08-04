<?php

namespace JobsBundle\Tool;

use JobsBundle\Model\ConnectorEngineInterface;

class FeedIdHelper
{
    protected array $feedIds;
    protected string $connectorToken;
    protected string $connectorDefinitionName;

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

    public function generateFeedId(): int
    {
        $latestFeedId = count($this->feedIds) === 0 ? 0 : max(array_column($this->feedIds, 'internalId'));

        $latestFeedId++;

        return $latestFeedId;
    }

    /**
     * @throws \Exception
     */
    public function addFeedId(int $internalFeedId, mixed $externalFeedId): void
    {
        $feedIndex = array_search($internalFeedId, array_column($this->feedIds, 'internalId'), true);

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
     * @throws \Exception
     */
    public function removeFeedId(int $internalFeedId): void
    {
        $feedIndex = array_search($internalFeedId, array_column($this->feedIds, 'internalId'), true);

        if ($feedIndex === false) {
            throw new \Exception(sprintf('Feed Id %d does not exist.', $internalFeedId));
        }

        unset($this->feedIds[$feedIndex]);

        $this->feedIds = array_values($this->feedIds);
    }

    public function findFeedId(int $internalId): ?array
    {
        $feedIndex = array_search($internalId, array_column($this->feedIds, 'internalId'), true);

        if ($feedIndex === false) {
            return null;
        }

        return $this->feedIds[$feedIndex];
    }

    public function generateFeedList(string $feedHost): array
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

    public function getAsArray(): array
    {
        return $this->feedIds;
    }
}
