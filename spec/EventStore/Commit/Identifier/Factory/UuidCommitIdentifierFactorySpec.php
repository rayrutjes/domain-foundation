<?php

namespace spec\RayRutjes\DomainFoundation\EventStore\Commit\Identifier\Factory;

use PhpSpec\ObjectBehavior;
use Rhumsaa\Uuid\Uuid;

class UuidCommitIdentifierFactorySpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->shouldHaveType('RayRutjes\DomainFoundation\EventStore\Commit\Identifier\Factory\UuidCommitIdentifierFactory');
        $this->shouldHaveType('RayRutjes\DomainFoundation\EventStore\Commit\Identifier\Factory\CommitIdentifierFactory');
    }

    public function it_creates_commit_identifiers()
    {
        $this->create(Uuid::NIL)->shouldHaveType('RayRutjes\DomainFoundation\EventStore\Commit\Identifier\UuidCommitIdentifier');
    }

    public function it_generates_unique_commit_identifiers()
    {
        $this->generate()->shouldHaveType('RayRutjes\DomainFoundation\EventStore\Commit\Identifier\UuidCommitIdentifier');
    }
}
