<?php

namespace spec\RayRutjes\DomainFoundation\Message\Identifier\Factory;

use PhpSpec\ObjectBehavior;
use Rhumsaa\Uuid\Uuid;

class UuidMessageIdentifierFactorySpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->shouldHaveType('RayRutjes\DomainFoundation\Message\Identifier\Factory\UuidMessageIdentifierFactory');
        $this->shouldHaveType('RayRutjes\DomainFoundation\Message\Identifier\Factory\MessageIdentifierFactory');
    }

    public function it_can_create_a_uuid_message_identifier_based_on_a_string_representation_of_a_uuid()
    {
        $this->create(Uuid::NIL)->shouldHaveType('RayRutjes\DomainFoundation\Message\Identifier\UuidMessageIdentifier');
    }

    public function it_can_generate_a_new_uuid_message_identifier()
    {
        $this->generate()->shouldHaveType('RayRutjes\DomainFoundation\Message\Identifier\UuidMessageIdentifier');
    }
}
