<?php

namespace RayRutjes\DomainFoundation\Command\Interceptor;

use RayRutjes\DomainFoundation\Command\Command;
use RayRutjes\DomainFoundation\Command\Handler\CommandHandler;
use RayRutjes\DomainFoundation\UnitOfWork\UnitOfWork;

final class DefaultInterceptorChain implements InterceptorChain
{
    /**
     * @var Command
     */
    private $command;

    /**
     * @var UnitOfWork
     */
    private $unitOfWork;

    /**
     * @var CommandHandler
     */
    private $commandHandler;

    /**
     * @var \ArrayIterator
     */
    private $handlerInterceptors;

    /**
     * @param Command                   $command
     * @param UnitOfWork                $unitOfWork
     * @param CommandHandler            $commandHandler
     * @param CommandHandlerInterceptor ...$handlerInterceptors
     */
    public function __construct(Command $command, UnitOfWork $unitOfWork, CommandHandler $commandHandler, /* HH_FIXME[4033]: variadic + strict */ ...$handlerInterceptors)
    {
        $this->command = $command;
        $this->unitOfWork = $unitOfWork;
        $this->commandHandler = $commandHandler;
        $this->handlerInterceptors = new \ArrayIterator($handlerInterceptors);
    }

    /**
     * @return mixed the result of the command handler, if any.
     */
    public function proceed()
    {
        $iterator = $this->handlerInterceptors;

        if ($iterator->valid()) {
            $handlerInterceptor = $iterator->current();
            $iterator->next();
            return $handlerInterceptor->handle($this->command, $this->unitOfWork, $this);
        } else {
            return $this->commandHandler->handle($this->command);
        }
    }
}
