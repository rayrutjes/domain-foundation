<?php

namespace RayRutjes\DomainFoundation\EventBus;

use RayRutjes\DomainFoundation\Domain\Event\Event;
use RayRutjes\DomainFoundation\EventBus\Listener\EventListener;

final class SimpleEventBus implements EventBus
{
    /**
     * @var array
     */
    private $listeners = [];

    /**
     * @param array $events
     */
    public function publish(array $events)
    {
        foreach ($events as $event) {
            if (!$event instanceof Event) {
                throw new \InvalidArgumentException('Only [RayRutjes\DomainFoundation\Domain\Event\Event] can be published.');
            }
            foreach ($this->listeners as $listener) {
                $listener->handle($event);
            }
        }
    }

    /**
     * @param EventListener $listener
     *
     * @throws \Exception
     */
    public function subscribe(EventListener $listener)
    {
        $key = $this->getListenerUniqueKey($listener);
        if (isset($this->listeners[$key])) {
            throw new \Exception('This listener instance is already subscribed.');
        }
        $this->listeners[$key] = $listener;
    }

    /**
     * @param EventListener $listener
     *
     * @throws \Exception
     */
    public function unsubscribe(EventListener $listener)
    {
        $key = $this->getListenerUniqueKey($listener);
        if (!isset($this->listeners[$key])) {
            throw new \Exception('This listener instance is not subscribed.');
        }
        unset($this->listeners[$key]);
    }

    /**
     * @param EventListener $listener
     *
     * @return string
     */
    private function getListenerUniqueKey(EventListener $listener)
    {
        return spl_object_hash($listener);
    }
}
