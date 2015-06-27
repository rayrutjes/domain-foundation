<?php

namespace RayRutjes\DomainFoundation\Message;

use RayRutjes\DomainFoundation\Contract\Contract;
use RayRutjes\DomainFoundation\Message\Identifier\MessageIdentifier;
use RayRutjes\DomainFoundation\Serializer\Serializable;

interface Message
{
    /**
     * @return MessageIdentifier
     */
    public function identifier();

    /**
     * @return Serializable
     */
    public function payload();

    /**
     * @return Contract
     */
    public function payloadType();

    /**
     * @return Metadata
     */
    public function metadata();

    /**
     * @return Contract
     */
    public function metadataType();
}
