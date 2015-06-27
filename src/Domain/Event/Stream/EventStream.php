<?php

namespace RayRutjes\DomainFoundation\Domain\Event\Stream;

use RayRutjes\DomainFoundation\Domain\Event\Event;

interface EventStream
{
    /**
     * @return bool
     */
    public function hasNext();

    /**
     * @return Event
     */
    public function next();

    /**
     * @return Event
     */
    public function peek();

    /**
     * @return bool
     */
    public function isEmpty();
}
