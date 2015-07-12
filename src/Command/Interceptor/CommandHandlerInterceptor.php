<?php

namespace RayRutjes\DomainFoundation\Command\Interceptor;

use RayRutjes\DomainFoundation\Command\Command;
use RayRutjes\DomainFoundation\UnitOfWork\UnitOfWork;

interface CommandHandlerInterceptor
{
    /**
     * @param Command          $command
     * @param UnitOfWork       $unitOfWork
     * @param InterceptorChain $interceptorChain
     *
     * @return mixed The result of the command handler, if any.
     */
    public function handle(Command $command, UnitOfWork $unitOfWork, InterceptorChain $interceptorChain);
}
