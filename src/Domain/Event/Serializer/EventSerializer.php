<?php

namespace RayRutjes\DomainFoundation\Domain\Event\Serializer;

use RayRutjes\DomainFoundation\Contract\Contract;
use RayRutjes\DomainFoundation\Domain\Event\Event;
use RayRutjes\DomainFoundation\Message\Metadata;
use RayRutjes\DomainFoundation\Serializer\Serializable;

interface EventSerializer
{
    /**
     * @param Event $event
     *
     * @return string
     */
    public function serializePayload(Event $event);

    /**
     * @param          $payload
     * @param Contract $type
     *
     * @return Serializable
     */
    public function deserializePayload($payload, Contract $type);

    /**
     * @param Event $event
     *
     * @return mixed
     */
    public function serializeMetadata(Event $event);

    /**
     * @param          $payload
     * @param Contract $type
     *
     * @return Metadata
     */
    public function deserializeMetadata($payload, Contract $type);
}
