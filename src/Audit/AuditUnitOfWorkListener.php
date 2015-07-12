<?php

namespace RayRutjes\DomainFoundation\Audit;

use RayRutjes\DomainFoundation\Audit\DataProvider\AuditDataProvider;
use RayRutjes\DomainFoundation\Audit\Logger\AuditLogger;
use RayRutjes\DomainFoundation\Command\Command;
use RayRutjes\DomainFoundation\Domain\Event\Event;
use RayRutjes\DomainFoundation\UnitOfWork\Listener\UnitOfWorkListenerAdapter;
use RayRutjes\DomainFoundation\UnitOfWork\UnitOfWork;

class AuditUnitOfWorkListener extends UnitOfWorkListenerAdapter
{
    /**
     * @var Command
     */
    private $command;

    /**
     * @var AuditDataProvider
     */
    private $auditDataProvider;

    /**
     * @var AuditLogger
     */
    private $auditLogger;

    /**
     * @var array
     */
    private $recordedEvents = [];

    /**
     * @var mixed
     */
    private $result;

    /**
     * @param Command           $command
     * @param AuditDataProvider $auditDataProvider
     * @param AuditLogger       $auditLogger
     */
    public function __construct(Command $command, AuditDataProvider $auditDataProvider, AuditLogger $auditLogger)
    {
        $this->command = $command;
        $this->auditDataProvider = $auditDataProvider;
        $this->auditLogger = $auditLogger;
    }

    /**
     * @param UnitOfWork $unitOfWork
     */
    public function afterCommit(UnitOfWork $unitOfWork)
    {
        $this->auditLogger->logSuccessful($this->command, $this->result, ...$this->recordedEvents);
    }

    /**
     * @param UnitOfWork $unitOfWork
     * @param \Exception $failureCause
     */
    public function onRollback(UnitOfWork $unitOfWork, \Exception $failureCause)
    {
        $this->auditLogger->logFailure($this->command, $failureCause, ...$this->recordedEvents);
    }


    /**
     * @param UnitOfWork $unitOfWork
     * @param Event      $event
     *
     * @return Event
     */
    public function onEventRegistration(UnitOfWork $unitOfWork, Event $event)
    {
        $auditData = $this->auditDataProvider->provideAuditDataFor($this->command);

        if (!empty($auditData)) {
            $event = $event->enrichMetadata($auditData);
        }

        $this->recordedEvents[] = $event;

        return $event;
    }

    /**
     * @param mixed $result
     */
    public function setResult($result)
    {
        $this->result = $result;
    }
}
