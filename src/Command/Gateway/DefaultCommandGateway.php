<?php

namespace RayRutjes\DomainFoundation\Command\Gateway;

use RayRutjes\DomainFoundation\Command\Bus\CommandBus;
use RayRutjes\DomainFoundation\Command\Command;
use RayRutjes\DomainFoundation\Command\Factory\CommandFactory;
use RayRutjes\DomainFoundation\Serializer\Serializable;

class DefaultCommandGateway implements CommandGateway
{
    /**
     * @var CommandBus
     */
    private $commandBus;

    /**
     * @var CommandFactory
     */
    private $commandFactory;

    /**
     * @param CommandBus     $commandBus
     * @param CommandFactory $commandFactory
     */
    public function __construct(CommandBus $commandBus, CommandFactory $commandFactory)
    {
        $this->commandBus = $commandBus;
        $this->commandFactory = $commandFactory;
    }

    /**
     * @param mixed $command
     */
    public function send($command)
    {
        $commandMessage = $this->wrapCommandWithMessageEnvelope($command);
        $this->commandBus->dispatch($commandMessage);
    }

    /**
     * @param mixed $command
     * @param int   $timeout Timeout in milliseconds.
     *
     * @return mixed
     */
    public function sendAndWait($command, $timeout = 300)
    {
        $commandMessage = $this->wrapCommandWithMessageEnvelope($command);

        // Todo: return future callback and pass it along for dispatching.
        $this->commandBus->dispatch($commandMessage);
    }

    /**
     * @param $command
     *
     * @return Command
     */
    private function wrapCommandWithMessageEnvelope($command)
    {
        if ($command instanceof Command) {
            return $command;
        }

        if (!$command instanceof Serializable) {
            throw new \InvalidArgumentException('Unsupported data type.');
        }

        return $this->commandFactory->createFromPayload($command);
    }
}
