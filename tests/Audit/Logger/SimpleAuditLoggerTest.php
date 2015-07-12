<?php

namespace RayRutjes\DomainFoundation\Test\Audit\Logger;

use RayRutjes\DomainFoundation\Audit\Logger\SimpleAuditLogger;
use RayRutjes\DomainFoundation\Command\GenericCommand;
use RayRutjes\DomainFoundation\Message\MessageIdentifier;
use RayRutjes\DomainFoundation\Test\Command\PayloadStub;

class SimpleAuditLoggerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var SimpleAuditLogger
     */
    private $auditLogger;

    private $logger;

    public function setUp()
    {
        $this->logger = $this->loggerMock();
        $this->auditLogger = new SimpleAuditLogger($this->logger);
    }

    public function testLogsAnInfoMessageWhenNotifiedOfCommandExecutionSuccess()
    {
        $this->logger->expects($this->once())
            ->method('info');

        $this->auditLogger->logSuccessful($this->commandMock(), 'result');
    }

    public function testLogsAWarningMessageWhenNotifiedOfCommandExecutionFailure()
    {
        $this->logger->expects($this->once())
            ->method('warning');

        $this->auditLogger->logFailure($this->commandMock(), new \Exception());
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function loggerMock()
    {
        return $this->getMock('Psr\Log\LoggerInterface');
    }

    /**
     * @return GenericCommand
     */
    private function commandMock()
    {
        return new GenericCommand(MessageIdentifier::generate(), new PayloadStub());
    }
}
