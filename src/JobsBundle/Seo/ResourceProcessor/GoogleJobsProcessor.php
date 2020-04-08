<?php

namespace JobsBundle\Seo\ResourceProcessor;

use JobsBundle\Connector\ConnectorServiceInterface;
use SeoBundle\Model\QueueEntryInterface;
use SeoBundle\ResourceProcessor\ResourceProcessorInterface;
use SeoBundle\Worker\WorkerResponseInterface;

class GoogleJobsProcessor implements ResourceProcessorInterface
{
    /**
     * @var string
     */
    protected $dataClass;

    /**
     * @var ConnectorServiceInterface
     */
    protected $connectorService;

    /**
     * @param string                    $dataClass
     * @param ConnectorServiceInterface $connectorService
     */
    public function __construct(string $dataClass, ConnectorServiceInterface $connectorService)
    {
        $this->dataClass = $dataClass;
        $this->connectorService = $connectorService;
    }

    /**
     * {@inheritDoc}
     */
    public function supportsWorker(string $workerIdentifier)
    {
        if (!$this->connectorService->connectorIsEnabled('google')) {
            return false;
        }

        return in_array($workerIdentifier, ['google_index']);
    }

    /**
     * {@inheritDoc}
     */
    public function processQueueEntry(QueueEntryInterface $queueEntry, string $workerIdentifier, $resource)
    {
        if (empty($this->dataClass)) {
            return null;
        }

        if (!class_exists($this->dataClass)) {
            return null;
        }

        if (!$resource instanceof $this->dataClass) {
            return null;
        }

        // @todo: listen to a "release" object flag before submitting to queue?

        $queueEntry->setDataType('pimcore_' . $resource->getType());
        $queueEntry->setDataId($resource->getId());
        $queueEntry->setDataUrl('http://www.solverat.com/job-' . $resource->getId());

        return $queueEntry;
    }

    /**
     * {@inheritDoc}
     */
    public function processWorkerResponse(WorkerResponseInterface $workerResponse)
    {
        // @todo: add custom "nice" log to a specific "job" tab in given data class?
    }
}
