<?php

namespace JobsBundle\Seo\ResourceProcessor;

use JobsBundle\Manager\LogManagerInterface;
use Pimcore\Model\DataObject\Concrete;
use SeoBundle\Exception\WorkerResponseInterceptException;
use SeoBundle\Model\QueueEntryInterface;
use SeoBundle\Worker\WorkerResponseInterface;
use SeoBundle\ResourceProcessor\ResourceProcessorInterface;
use JobsBundle\Service\EnvironmentServiceInterface;
use JobsBundle\Connector\ConnectorServiceInterface;
use JobsBundle\Context\ContextServiceInterface;
use JobsBundle\Context\ResolvedItemInterface;

class GoogleJobsProcessor implements ResourceProcessorInterface
{
    protected EnvironmentServiceInterface $environmentService;
    protected ContextServiceInterface $contextService;
    protected ConnectorServiceInterface $connectorService;
    protected LogManagerInterface $logManager;

    public function __construct(
        EnvironmentServiceInterface $environmentService,
        ContextServiceInterface $contextService,
        ConnectorServiceInterface $connectorService,
        LogManagerInterface $logManager
    ) {
        $this->environmentService = $environmentService;
        $this->contextService = $contextService;
        $this->connectorService = $connectorService;
        $this->logManager = $logManager;
    }

    public function supportsWorker(string $workerIdentifier): bool
    {
        if (!$this->connectorService->connectorDefinitionIsEnabled('google')) {
            return false;
        }

        $connectorDefinition = $this->connectorService->getConnectorDefinition('google', true);
        if (!$connectorDefinition->isOnline()) {
            return false;
        }

        return $workerIdentifier === 'google_index';
    }

    public function supportsResource(mixed $resource): bool
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

    public function generateQueueContext(mixed $resource): array
    {
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

    public function processQueueEntry(QueueEntryInterface $queueEntry, string $workerIdentifier, array $context, $resource): ?QueueEntryInterface
    {
        /** @var ResolvedItemInterface $resolvedItem */
        $resolvedItem = $context['resolvedItem'];

        $queueEntry->setDataType($resolvedItem->getResolvedParam('type'));
        $queueEntry->setDataId($resolvedItem->getResolvedParam('dataId'));
        $queueEntry->setDataUrl($resolvedItem->getResolvedParam('dataUrl'));

        return $queueEntry;
    }

    public function processWorkerResponse(WorkerResponseInterface $workerResponse): void
    {
        $queueEntry = $workerResponse->getQueueEntry();

        $log = $this->logManager->createNewForConnector('google');

        if ($workerResponse->getStatus() === 200) {
            $status = 'success';
        } elseif ($workerResponse->getStatus() === 500) {
            $status = 'fatal';
        } else {
            $status = 'error';
        }

        $log->setType($status);
        $log->setMessage($workerResponse->getMessage());
        $log->setObjectId($queueEntry->getDataId());

        $this->logManager->update($log);

        // intercept further logging in seo bundle.
        throw new WorkerResponseInterceptException();
    }
}
