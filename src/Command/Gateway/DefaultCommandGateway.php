<?php

namespace RayRutjes\DomainFoundation\Command\Gateway;

use RayRutjes\DomainFoundation\Command\Bus\CommandBus;
use RayRutjes\DomainFoundation\Command\Command;
use RayRutjes\DomainFoundation\Command\GenericCommand;
use RayRutjes\DomainFoundation\Message\MessageIdentifier;
use RayRutjes\DomainFoundation\Serializer\Serializable;

class DefaultCommandGateway implements CommandGateway
{
    /**
     * @var CommandBus
     */
    private $commandBus;

    /**
     * @param CommandBus $commandBus
     */
    public function __construct(CommandBus $commandBus)
    {
        $this->commandBus = $commandBus;
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

        if ($command instanceof Serializable) {
            return new GenericCommand(MessageIdentifier::generate(), $command);
        }

        throw new \InvalidArgumentException('Unsupported data type.');
    }
}
