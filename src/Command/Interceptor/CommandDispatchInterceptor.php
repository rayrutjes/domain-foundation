<?php

namespace RayRutjes\DomainFoundation\Command\Interceptor;

use RayRutjes\DomainFoundation\Command\Command;

interface CommandDispatchInterceptor
{
    /**
     * @param Command $command
     *
     * @return Command
     */
    public function handle(Command $command);
}
