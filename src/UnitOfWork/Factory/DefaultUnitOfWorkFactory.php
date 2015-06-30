<?php

namespace RayRutjes\DomainFoundation\UnitOfWork\Factory;

use RayRutjes\DomainFoundation\UnitOfWork\AggregateContainer\Factory\DefaultAggregateContainerFactory;
use RayRutjes\DomainFoundation\UnitOfWork\DefaultUnitOfWork;
use RayRutjes\DomainFoundation\UnitOfWork\EventRegistrationCallback\Factory\DefaultUnitOfWorkEventRegistrationCallbackFactory;
use RayRutjes\DomainFoundation\UnitOfWork\Listener\UnitOfWorkListenerCollection;
use RayRutjes\DomainFoundation\UnitOfWork\StagingEventContainer\Factory\DefaultStagingEventContainerFactory;
use RayRutjes\DomainFoundation\UnitOfWork\TransactionManager;
use RayRutjes\DomainFoundation\UnitOfWork\UnitOfWork;

class DefaultUnitOfWorkFactory implements UnitOfWorkFactory
{
    /**
     * @var TransactionManager
     */
    private $transactionManager;

    /**
     * @param TransactionManager $transactionManager
     */
    public function __construct(TransactionManager $transactionManager = null)
    {
        $this->transactionManager = $transactionManager;
    }

    /**
     * @return UnitOfWork
     */
    public function createUnitOfWork()
    {
        return new DefaultUnitOfWork(
            new UnitOfWorkListenerCollection(),
            new DefaultStagingEventContainerFactory(),
            new DefaultAggregateContainerFactory(),
            new DefaultUnitOfWorkEventRegistrationCallbackFactory(),
            $this->transactionManager
        );
    }
}
