<?php

namespace RayRutjes\DomainFoundation\Test\Audit;

use RayRutjes\DomainFoundation\Audit\AuditInterceptor;
use RayRutjes\DomainFoundation\Command\GenericCommand;
use RayRutjes\DomainFoundation\Message\MessageIdentifier;
use RayRutjes\DomainFoundation\Test\Persistence\Pdo\EventStore\PayloadStub;

class AuditInterceptorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var AuditInterceptor
     */
    private $interceptor;

    private $auditLogger;

    private $auditDataProvider;

    public function setUp()
    {
        $this->auditDataProvider = $this->auditDataProviderMock();
        $this->auditLogger = $this->auditLoggerMock();
        $this->interceptor = new AuditInterceptor($this->auditDataProvider, $this->auditLogger);
    }

    public function testRegistersAnAuditListenerToTheUnitOfWorkWhenInterceptsACommand()
    {
        $command = $this->commandMock();
        $unitOfWork = $this->unitOfWorkMock();
        $interceptorChain = $this->interceptorChainMock();

        $unitOfWork->expects($this->once())
            ->method('registerListener')
            ->with($this->isInstanceOf('RayRutjes\DomainFoundation\Audit\AuditUnitOfWorkListener'));


        $this->interceptor->handle($command, $unitOfWork, $interceptorChain);
    }

    public function testTellsTheInterceptorChainToProceed()
    {
        $command = $this->commandMock();
        $unitOfWork = $this->unitOfWorkMock();
        $interceptorChain = $this->interceptorChainMock();

        $interceptorChain->expects($this->once())
            ->method('proceed')
        ->willReturn('result');

        $this->assertEquals('result', $this->interceptor->handle($command, $unitOfWork, $interceptorChain));
    }

    private function interceptorChainMock()
    {
        return $this->getMock('RayRutjes\DomainFoundation\Command\Interceptor\InterceptorChain');
    }

    private function auditDataProviderMock()
    {
        return $this->getMock('RayRutjes\DomainFoundation\Audit\DataProvider\AuditDataProvider');
    }

    private function auditLoggerMock()
    {
        return $this->getMock('RayRutjes\DomainFoundation\Audit\Logger\AuditLogger');
    }

    private function commandMock()
    {
        return new GenericCommand(MessageIdentifier::generate(), new PayloadStub());
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
