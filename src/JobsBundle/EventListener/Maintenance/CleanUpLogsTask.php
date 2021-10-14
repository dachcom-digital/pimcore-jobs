<?php

namespace JobsBundle\EventListener\Maintenance;

use JobsBundle\Repository\LogRepositoryInterface;
use Pimcore\Maintenance\TaskInterface;

class CleanUpLogsTask implements TaskInterface
{
    protected int $logExpirationDays;
    protected LogRepositoryInterface $logRepository;

    public function __construct(int $logExpirationDays, LogRepositoryInterface $logRepository)
    {
        $this->logExpirationDays = $logExpirationDays;
        $this->logRepository = $logRepository;
    }

    public function execute(): void
    {
        $this->logRepository->deleteExpired($this->logExpirationDays);
    }
}
