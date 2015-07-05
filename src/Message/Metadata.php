<?php

namespace RayRutjes\DomainFoundation\Message;

use RayRutjes\DomainFoundation\Serializer\Serializable;

class Metadata implements Serializable
{
    /**
     * @var array
     */
    private $metadata;

    /**
     * @param array $metadata
     */
    public function __construct(array $metadata = [])
    {
        $this->metadata = $metadata;
    }

    /**
     * @return array
     */
    public function all()
    {
        return $this->metadata;
    }

    /**
     * @param $key
     *
     * @return mixed
     */
    public function get($key)
    {
        if (array_key_exists($key, $this->metadata)) {
            return $this->metadata[$key];
        }
    }

    /**
     * @param array $metadata
     *
     * @return Metadata
     */
    public function mergeWith(array $metadata)
    {
        return new self(array_merge($this->metadata, $metadata));
    }
}
