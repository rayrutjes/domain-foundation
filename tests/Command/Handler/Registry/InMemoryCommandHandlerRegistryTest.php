<?php

namespace RayRutjes\DomainFoundation\Test\Command\Handler\Registry;

use RayRutjes\DomainFoundation\Command\Handler\Registry\InMemoryCommandHandlerRegistry;

class InMemoryCommandHandlerRegistryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var InMemoryCommandHandlerRegistry
     */
    private $handlerRegistry;

    public function setUp()
    {
        $this->handlerRegistry = new InMemoryCommandHandlerRegistry();
    }

    public function testImplementsCommandHandlerRegistryInterface()
    {
        $this->assertInstanceOf('RayRutjes\DomainFoundation\Command\Handler\Registry\CommandHandlerRegistry', $this->handlerRegistry);
    }

    public function testRetrievesACommandHandler()
    {
        $command = $this->commandMock();
        $command->method('commandName')
                ->willReturn('CommandName');

        $handler = $this->commandHandlerMock();

        $this->handlerRegistry->subscribe('CommandName', $handler);

        $this->assertSame($handler, $this->handlerRegistry->findCommandHandlerFor($command));
    }

    /**
     * @expectedException \RayRutjes\DomainFoundation\Command\Handler\Registry\CommandHandlerNotFoundException
     */
    public function testThrowsAnExceptionIfNoCommandHandlerWasFound()
    {
        $command = $this->commandMock();

        $this->handlerRegistry->findCommandHandlerFor($command);
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function commandMock()
    {
        return $this->getMock('RayRutjes\DomainFoundation\Command\Command');
    }

    /**
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function commandHandlerMock()
    {
        return $this->getMock('RayRutjes\DomainFoundation\Command\Handler\CommandHandler');
    }
}
