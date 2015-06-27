<?php

namespace RayRutjes\DomainFoundation\Domain\Event\Serializer;

use RayRutjes\DomainFoundation\Contract\Contract;
use RayRutjes\DomainFoundation\Domain\Event\Event;
use RayRutjes\DomainFoundation\Message\Metadata;
use RayRutjes\DomainFoundation\Serializer\Serializable;
use RayRutjes\DomainFoundation\Serializer\Serializer;

class CompositeEventSerializer implements EventSerializer
{
    /**
     * @var Serializer
     */
    private $serializer;

    /**
     * @param Serializer $serializer
     */
    public function __construct(Serializer $serializer)
    {
        $this->serializer = $serializer;
    }

    /**
     * @param Event $event
     *
     * @return mixed
     */
    public function serializePayload(Event $event)
    {
        return $this->serializer->serialize($event->payload());
    }

    /**
     * @param          $payload
     * @param Contract $type
     *
     * @return Serializable
     */
    public function deserializePayload($payload, Contract $type)
    {
        return $this->serializer->deserialize($payload, $type);
    }

    /**
     * @param Event $event
     *
     * @return mixed
     */
    public function serializeMetadata(Event $event)
    {
        return $this->serializer->serialize($event->metadata());
    }

    /**
     * @param          $payload
     * @param Contract $type
     *
     * @return Metadata
     */
    public function deserializeMetadata($payload, Contract $type)
    {
        return $this->serializer->deserialize($payload, $type);
    }
}
