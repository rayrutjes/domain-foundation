<?php

namespace RayRutjes\DomainFoundation\Serializer;

use RayRutjes\DomainFoundation\Contract\Contract;

interface Serializer
{
    /**
     * @param $object
     *
     * @return mixed
     */
    public function serialize(Serializable $object);

    /**
     * @param          $object
     * @param Contract $type
     *
     * @return Serializable
     */
    public function deserialize($object, Contract $type);
}
