<?php

namespace RayRutjes\DomainFoundation\Test\Command\Callback;

use RayRutjes\DomainFoundation\Command\Callback\LogCommandCallback;

class LogCommandCallbackTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var LogCommandCallback
     */
    private $callback;

    private $logger;

    public function setUp()
    {
        $command = $this->getMockBuilder('RayRutjes\DomainFoundation\Command\Command')->getMock();
        $this->logger = $this->getMockBuilder('Psr\Log\LoggerInterface')->getMock();

        $this->callback = new LogCommandCallback($command, $this->logger);
    }

    public function testImplementsCommandCallbackInterface()
    {
        $this->assertInstanceOf('RayRutjes\DomainFoundation\Command\Callback\CommandCallback', $this->callback);
    }

    public function testLogsAnInfoMessageOnSuccess()
    {
        $this->logger->expects($this->once())
                     ->method('info');

        $this->callback->onSuccess();
    }

    public function testLogsAWarningMessageOnFailure()
    {
        $this->logger->expects($this->once())
                     ->method('warning');

        $this->callback->onFailure(new \Exception());
    }
}
