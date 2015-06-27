<?php

namespace RayRutjes\DomainFoundation\Stub\Domain\AggregateRoot;

use RayRutjes\DomainFoundation\Domain\AggregateRoot\AggregateRootIdentifier;
use RayRutjes\DomainFoundation\Domain\AggregateRoot\EventSourcedAggregateRoot;
use RayRutjes\DomainFoundation\Stub\Domain\Event\Container\Factory\ProxyEventContainerFactoryStub;
use RayRutjes\DomainFoundation\Stub\Domain\Event\Stream\Factory\ProxyEventStreamFactoryStub;
use spec\RayRutjes\DomainFoundation\Domain\AggregateRoot\Created;
use spec\RayRutjes\DomainFoundation\Domain\AggregateRoot\Deleted;

class EventSourcedAggregateRootStub extends EventSourcedAggregateRoot
{
    private $isDeleted = false;

    private $eventStreamFactory;

    private $eventContainerFactory;

    public static function create(
        AggregateRootIdentifier $identifier,
        ProxyEventContainerFactoryStub $eventContainerFactory,
        ProxyEventStreamFactoryStub $eventStreamFactory
    ) {
        $instance = new static();

        // Here we stub the factories for testing purpose.
        $instance->setEventContainerFactoryProxy($eventContainerFactory);
        $instance->setEventStreamFactoryProxy($eventStreamFactory);

        $instance->applyChange(new Created($identifier));

        return $instance;
    }

    public static function wrongInitialization()
    {
        return new static();
    }

    public function overrideIdentifier(AggregateRootIdentifier $identifier)
    {
        $this->setIdentifier($identifier);
    }

    protected function eventStreamFactory()
    {
        return $this->eventStreamFactory;
    }

    public function setEventStreamFactoryProxy(ProxyEventStreamFactoryStub $proxy)
    {
        $proxy->proxy(parent::eventStreamFactory());
        $this->eventStreamFactory = $proxy;
    }

    protected function eventContainerFactory()
    {
        return $this->eventContainerFactory;
    }

    public function setEventContainerFactoryProxy(ProxyEventContainerFactoryStub $proxy)
    {
        $proxy->proxy(parent::eventContainerFactory());
        $this->eventContainerFactory = $proxy;
    }

    protected function applyCreated(Created $created)
    {
        $this->setIdentifier($created->identifier());
    }

    protected function applyDeleted(Deleted $deleted)
    {
        $this->isDeleted = true;
    }

    public function isDeleted()
    {
        return $this->isDeleted;
    }
}
