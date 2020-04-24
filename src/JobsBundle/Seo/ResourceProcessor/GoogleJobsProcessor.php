<?php

namespace JobsBundle\Seo\ResourceProcessor;

use Pimcore\Model\DataObject\Concrete;
use SeoBundle\Model\QueueEntryInterface;
use SeoBundle\Worker\WorkerResponseInterface;
use SeoBundle\ResourceProcessor\ResourceProcessorInterface;
use JobsBundle\Service\EnvironmentServiceInterface;
use JobsBundle\Connector\ConnectorServiceInterface;
use JobsBundle\Context\ContextServiceInterface;
use JobsBundle\Context\ResolvedItemInterface;

class GoogleJobsProcessor implements ResourceProcessorInterface
{
    /**
     * @var EnvironmentServiceInterface
     */
    protected $environmentService;

    /**
     * @var ContextServiceInterface
     */
    protected $contextService;

    /**
     * @var ConnectorServiceInterface
     */
    protected $connectorService;

    /**
     * @param EnvironmentServiceInterface $environmentService
     * @param ContextServiceInterface     $contextService
     * @param ConnectorServiceInterface   $connectorService
     */
    public function __construct(
        EnvironmentServiceInterface $environmentService,
        ContextServiceInterface $contextService,
        ConnectorServiceInterface $connectorService
    ) {
        $this->environmentService = $environmentService;
        $this->contextService = $contextService;
        $this->connectorService = $connectorService;
    }

    /**
     * {@inheritdoc}
     */
    public function supportsWorker(string $workerIdentifier)
    {
        if (!$this->connectorService->connectorDefinitionIsEnabled('google')) {
            return false;
        }

        $connectorDefinition = $this->connectorService->getConnectorDefinition('google', true);
        if (!$connectorDefinition->isOnline()) {
            return false;
        }

        return in_array($workerIdentifier, ['google_index']);
    }

    /**
     * {@inheritdoc}
     */
    public function supportsResource($resource)
    {
        if (empty($this->environmentService->getDataClass())) {
            return false;
        }

        $classPath = sprintf('Pimcore\Model\DataObject\%s', $this->environmentService->getDataClass());
        if (!class_exists($classPath)) {
            return false;
        }

        if (!$resource instanceof $classPath) {
            return false;
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function generateQueueContext($resource)
    {
        // which feed is it??
        $queueContext = [];

        if (!$resource instanceof Concrete) {
            return [];
        }

        $connectorDefinition = $this->connectorService->getConnectorDefinition('google', true);
        $resolvedItems = $this->contextService->resolveContextItems('seo_queue', $connectorDefinition, ['resource' => $resource]);

        foreach ($resolvedItems as $resolvedItem) {
            $queueContext[] = ['resolvedItem' => $resolvedItem];
        }

        return $queueContext;
    }

    /**
     * {@inheritdoc}
     */
    public function processQueueEntry(QueueEntryInterface $queueEntry, string $workerIdentifier, array $context, $resource)
    {
        $dataUrl = null;

        /** @var ResolvedItemInterface $resolvedItem */
        $resolvedItem = $context['resolvedItem'];

        $queueEntry->setDataType($resolvedItem->getResolvedParam('type'));
        $queueEntry->setDataId($resolvedItem->getResolvedParam('dataId'));
        $queueEntry->setDataUrl($resolvedItem->getResolvedParam('dataUrl'));

        return $queueEntry;
    }

    /**
     * {@inheritdoc}
     */
    public function processWorkerResponse(WorkerResponseInterface $workerResponse)
    {
        // @todo: add custom "nice" log to a specific "job" tab in given data class?
    }
}
