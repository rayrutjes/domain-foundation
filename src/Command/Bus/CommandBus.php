<?php

namespace RayRutjes\DomainFoundation\Command\Bus;

use RayRutjes\DomainFoundation\Command\Callback\CommandCallback;
use RayRutjes\DomainFoundation\Command\Command;

interface CommandBus
{
    /**
     * @param Command         $command
     * @param CommandCallback $callback
     */
    public function dispatch(Command $command, CommandCallback $callback = null);
}
