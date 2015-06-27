<?php

namespace RayRutjes\DomainFoundation\Command\Handler\Registry;

use RayRutjes\DomainFoundation\Command\Command;
use RayRutjes\DomainFoundation\Command\Handler\CommandHandler;

final class InMemoryCommandHandlerRegistry implements CommandHandlerRegistry
{
    /**
     * @var array
     */
    private $subscriptions = [];

    /**
     * @param Command $command
     *
     * @return CommandHandler
     *
     * @throws CommandHandlerNotFoundException
     */
    public function findCommandHandlerFor(Command $command)
    {
        if (!isset($this->subscriptions[$command->commandName()])) {
            throw new CommandHandlerNotFoundException();
        }

        return $this->subscriptions[$command->commandName()];
    }

    /**
     * @param $commandName
     * @param CommandHandler $handler
     */
    public function subscribe($commandName, CommandHandler $handler)
    {
        $this->subscriptions[$commandName] = $handler;
    }
}
