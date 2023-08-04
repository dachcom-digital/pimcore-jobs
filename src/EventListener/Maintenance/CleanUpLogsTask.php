<?php

namespace JobsBundle\EventListener\Maintenance;

use JobsBundle\Repository\LogRepositoryInterface;
use Pimcore\Maintenance\TaskInterface;

class CleanUpLogsTask implements TaskInterface
{
    public function __construct(
        protected int $logExpirationDays,
        protected LogRepositoryInterface $logRepository
    ) {
    }

    public function execute(): void
    {
        $this->logRepository->deleteExpired($this->logExpirationDays);
    }
}
