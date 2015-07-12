<?php

namespace RayRutjes\DomainFoundation\Command\Bus;

use RayRutjes\DomainFoundation\Command\Callback\CommandCallback;
use RayRutjes\DomainFoundation\Command\Command;
use RayRutjes\DomainFoundation\Command\Handler\Registry\CommandHandlerRegistry;
use RayRutjes\DomainFoundation\Command\Interceptor\CommandDispatchInterceptor;
use RayRutjes\DomainFoundation\Command\Interceptor\CommandHandlerInterceptor;
use RayRutjes\DomainFoundation\Command\Interceptor\DefaultInterceptorChain;
use RayRutjes\DomainFoundation\UnitOfWork\UnitOfWork;

final class SimpleCommandBus implements CommandBus
{
    /**
     * @var CommandHandlerRegistry
     */
    private $handlerRegistry;

    /**
     * @var UnitOfWork
     */
    private $unitOfWork;

    /**
     * @var array
     */
    private $dispatchInterceptors = [];

    /**
     * @var array
     */
    private $handlerInterceptors = [];

    /**
     * @param CommandHandlerRegistry $handlerRegistry
     * @param UnitOfWork             $unitOfWork
     */
    public function __construct(CommandHandlerRegistry $handlerRegistry, UnitOfWork $unitOfWork)
    {
        $this->handlerRegistry = $handlerRegistry;
        $this->unitOfWork = $unitOfWork;
    }

    /**
     * @param Command         $command
     * @param CommandCallback $callback
     *
     * @throws \Exception
     */
    public function dispatch(Command $command, CommandCallback $callback = null)
    {
        $command = $this->intercept($command);

        $handler = $this->handlerRegistry->findCommandHandlerFor($command);

        $this->unitOfWork->start();
        $interceptorChain = new DefaultInterceptorChain($command, $this->unitOfWork, $handler, ...$this->handlerInterceptors);

        try {
            $result = $interceptorChain->proceed();
            if (null !== $callback) {
                $callback->onSuccess($result);
            }
        } catch (\Exception $exception) {
            $this->unitOfWork->rollback($exception);
            if (null !== $callback) {
                $callback->onFailure($exception);
            }

            throw $exception;
        }

        $this->unitOfWork->commit();
    }

    /**
     * @param Command $command
     *
     * @return Command
     */
    private function intercept(Command $command)
    {
        foreach ($this->dispatchInterceptors as $dispatchInterceptor) {
            $command = $dispatchInterceptor->handle($command);
        }

        return $command;
    }

    /**
     * @param CommandDispatchInterceptor ...$dispatchInterceptors
     */
    public function addDispatchInterceptor(/* HH_FIXME[4033]: variadic + strict */ ...$dispatchInterceptors)
    {
        foreach ($dispatchInterceptors as $dispatchInterceptor) {
            if (!$dispatchInterceptor instanceof CommandDispatchInterceptor) {
                throw new \InvalidArgumentException(sprintf('Expected %s', CommandDispatchInterceptor::class));
            }
        }
        $this->dispatchInterceptors = $dispatchInterceptors;
    }

    /**
     * @param CommandHandlerInterceptor ...$handlerInterceptors
     */
    public function addHandlerInterceptor(/* HH_FIXME[4033]: variadic + strict */ ...$handlerInterceptors)
    {
        foreach ($handlerInterceptors as $handlerInterceptor) {
            if (!$handlerInterceptor instanceof CommandHandlerInterceptor) {
                throw new \InvalidArgumentException(sprintf('Expected %s', CommandHandlerInterceptor::class));
            }
        }
        $this->handlerInterceptors = $handlerInterceptors;
    }
}
