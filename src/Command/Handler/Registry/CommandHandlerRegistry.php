<?php

namespace RayRutjes\DomainFoundation\Command\Handler\Registry;

use RayRutjes\DomainFoundation\Command\Command;
use RayRutjes\DomainFoundation\Command\Handler\CommandHandler;

interface CommandHandlerRegistry
{
    /**
     * @param Command $command
     *
     * @return CommandHandler
     *
     * @throws CommandHandlerNotFoundException
     */
    public function findCommandHandlerFor(Command $command);

    /**
     * @param                $commandName
     * @param CommandHandler $handler
     */
    public function subscribe($commandName, CommandHandler $handler);
}
