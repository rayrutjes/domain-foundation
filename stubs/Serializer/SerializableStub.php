<?php

namespace RayRutjes\DomainFoundation\Stub\Serializer;

use RayRutjes\DomainFoundation\Serializer\Serializable;

class SerializableStub implements Serializable
{
    private $name;

    public $surname;

    public function __construct($name = 'test')
    {
        $this->name = $name;
        $this->surname = $name;
    }

    public function name()
    {
        return $this->name;
    }
}
