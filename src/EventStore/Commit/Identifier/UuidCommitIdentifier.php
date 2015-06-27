<?php

namespace RayRutjes\DomainFoundation\EventStore\Commit\Identifier;

use RayRutjes\DomainFoundation\ValueObject\Identity\Uuid;

final class UuidCommitIdentifier extends Uuid implements CommitIdentifier
{
}
