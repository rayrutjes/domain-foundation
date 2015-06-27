<?php

namespace RayRutjes\DomainFoundation\Command\Bus;

use RayRutjes\DomainFoundation\Command\Callback\CommandCallback;
use RayRutjes\DomainFoundation\Command\Command;
use RayRutjes\DomainFoundation\Command\Handler\Registry\CommandHandlerRegistry;
use RayRutjes\DomainFoundation\UnitOfWork\Factory\UnitOfWorkFactory;

final class SimpleCommandBus implements CommandBus
{
    /**
     * @var CommandHandlerRegistry
     */
    private $handlerRegistry;

    /**
     * @var UnitOfWorkFactory
     */
    private $unitOfWorkFactory;

    /**
     * @param CommandHandlerRegistry $handlerRegistry
     * @param UnitOfWorkFactory      $unitOfWorkFactory
     */
    public function __construct(CommandHandlerRegistry $handlerRegistry, UnitOfWorkFactory $unitOfWorkFactory)
    {
        $this->handlerRegistry = $handlerRegistry;
        $this->unitOfWorkFactory = $unitOfWorkFactory;
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

        $unitOfWork = $this->unitOfWorkFactory->createUnitOfWork();
        $unitOfWork->start();

        try {
            $result = $handler->handle($command);
            if (null !== $callback) {
                $callback->onSuccess($result);
            }
        } catch (\Exception $exception) {
            $unitOfWork->rollback($exception);
            if (null !== $callback) {
                $callback->onFailure($exception);
            }

            // Todo: remove this ?
            throw $exception;
        }

        $unitOfWork->commit();
    }
}
