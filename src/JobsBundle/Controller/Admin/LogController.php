<?php

namespace JobsBundle\Controller\Admin;

use JobsBundle\Manager\LogManagerInterface;
use JobsBundle\Model\LogEntryInterface;
use Pimcore\Bundle\AdminBundle\Controller\AdminAbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class LogController extends AdminAbstractController
{
    protected LogManagerInterface $logManager;

    public function __construct(LogManagerInterface $logManager)
    {
        $this->logManager = $logManager;
    }

    public function loadLogsForObjectAction(Request $request, int $connectorEngineId, int $objectId): JsonResponse
    {
        $items = [];
        $offset = (int) $request->get('start', 0);
        $limit = (int) $request->get('limit', 25);

        try {
            $logEntriesPaginator = $this->logManager->getForConnectorEngineAndObject($connectorEngineId, $objectId, $offset, $limit);
        } catch (\Exception $e) {
            return $this->adminJson(['success' => false, 'entries' => [], 'limit' => 0, 'total' => 0]);
        }

        $logEntriesPaginator->getQuery()
            ->setFirstResult($offset)
            ->setMaxResults($limit);

        /** @var LogEntryInterface $entry */
        foreach ($logEntriesPaginator as $entry) {
            $items[] = [
                'id'      => $entry->getId(),
                'type'    => $entry->getType(),
                'message' => $entry->getMessage(),
                'date'    => $entry->getCreationDate()->format('d.m.Y H:i')
            ];
        }

        return $this->adminJson([
            'entries' => $items,
            'limit'   => $limit,
            'total'   => $logEntriesPaginator->count()
        ]);
    }

    public function removeLogsForObjectAction(Request $request, int $connectorEngineId, int $objectId): JsonResponse
    {
        try {
            $this->logManager->deleteForConnectorEngineAndObject($connectorEngineId, $objectId);
        } catch (\Exception $e) {
            return $this->adminJson(['success' => false]);
        }

        return $this->adminJson([
            'success' => true
        ]);
    }

    public function flushLogsAction(Request $request): JsonResponse
    {
        try {
            $this->logManager->flushLogs();
        } catch (\Exception $e) {
            return $this->adminJson(['success' => false, 'message' => $e->getMessage()]);
        }

        return $this->adminJson([
            'success' => true
        ]);
    }
}
