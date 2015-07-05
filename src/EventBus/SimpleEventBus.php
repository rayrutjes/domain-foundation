<?php

namespace RayRutjes\DomainFoundation\EventBus;

use RayRutjes\DomainFoundation\Domain\Event\Event;

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
     */
    public function subscribe(EventListener $listener)
    {
        $key = $this->getListenerUniqueKey($listener);
        $this->listeners[$key] = $listener;
    }

    /**
     * @param EventListener $listener
     */
    public function unsubscribe(EventListener $listener)
    {
        $key = $this->getListenerUniqueKey($listener);
        if (isset($this->listeners[$key])) {
            unset($this->listeners[$key]);
        }
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
