<?php

namespace RayRutjes\DomainFoundation\Test\Audit;

use RayRutjes\DomainFoundation\Audit\AuditUnitOfWorkListener;
use RayRutjes\DomainFoundation\Command\GenericCommand;
use RayRutjes\DomainFoundation\Message\MessageIdentifier;
use RayRutjes\DomainFoundation\Test\Command\PayloadStub;

class AuditUnitOfWorkListenerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var AuditUnitOfWorkListener
     */
    private $listener;

    private $command;

    private $auditDataProvider;

    private $auditLogger;

    private $result;

    public function setUp()
    {
        $this->command = new GenericCommand(MessageIdentifier::generate(), new PayloadStub());
        $this->auditDataProvider = $this->auditDataProviderMock();
        $this->auditLogger = $this->auditLoggerMock();
        $this->result = 'result';

        $this->listener = new AuditUnitOfWorkListener($this->command, $this->auditDataProvider, $this->auditLogger);
        $this->listener->setResult($this->result);
    }

    public function testLogsCommandExecutionSuccessAfterCommit()
    {
        $this->auditLogger->expects($this->once())
            ->method('logSuccessful')
            ->with($this->identicalTo($this->command), $this->identicalTo($this->result));

        $this->listener->afterCommit($this->unitOfWorkMock());
    }

    public function testLogsCommandExecutionFailureOnRollback()
    {
        $failureCause = new \Exception();
        $this->auditLogger->expects($this->once())
            ->method('logFailure')
            ->with($this->identicalTo($this->command), $this->identicalTo($failureCause));

        $this->listener->onRollback($this->unitOfWorkMock(), $failureCause);
    }

    public function testEnrichesEventMetadataWithAuditData()
    {
        $auditData = ['command_identifier' => 'commandidentifier'];
        $this->auditDataProvider->method('provideAuditDataFor')
            ->with($this->command)
            ->willReturn($auditData);

        $event = $this->eventMock();
        $enrichedEvent = $this->eventMock();
        $event->expects($this->once())
            ->method('enrichMetadata')
            ->with($this->equalTo($auditData))
            ->willReturn($enrichedEvent);

        $this->assertSame($enrichedEvent, $this->listener->onEventRegistration($this->unitOfWorkMock(), $event));

        return $this->listener;
    }



    private function auditDataProviderMock()
    {
        return $this->getMock('RayRutjes\DomainFoundation\Audit\DataProvider\AuditDataProvider');
    }

    private function auditLoggerMock()
    {
        return $this->getMock('RayRutjes\DomainFoundation\Audit\Logger\AuditLogger');
    }

    private function unitOfWorkMock()
    {
        return $this->getMock('RayRutjes\DomainFoundation\UnitOfWork\UnitOfWork');
    }

    private function eventMock()
    {
        return $this->getMock('RayRutjes\DomainFoundation\Domain\Event\Event');
    }
}
