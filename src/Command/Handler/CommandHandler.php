<?php

namespace RayRutjes\DomainFoundation\Command\Handler;

use RayRutjes\DomainFoundation\Command\Command;

interface CommandHandler
{
    /**
     * @param Command $command
     *
     * @return mixed
     */
    public function handle(Command $command);
}
