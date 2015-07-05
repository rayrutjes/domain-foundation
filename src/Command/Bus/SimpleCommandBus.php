<?php

namespace RayRutjes\DomainFoundation\Command\Bus;

use RayRutjes\DomainFoundation\Command\Callback\CommandCallback;
use RayRutjes\DomainFoundation\Command\Command;
use RayRutjes\DomainFoundation\Command\Handler\Registry\CommandHandlerRegistry;
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
        $handler = $this->handlerRegistry->findCommandHandlerFor($command);

        $this->unitOfWork->start();

        try {
            $result = $handler->handle($command);
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
}
