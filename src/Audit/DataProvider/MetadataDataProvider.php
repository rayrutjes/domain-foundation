<?php

namespace RayRutjes\DomainFoundation\Audit\DataProvider;

use RayRutjes\DomainFoundation\Command\Command;

class MetadataDataProvider implements AuditDataProvider
{
    /**
     * @param Command $command
     *
     * @return array
     */
    public function provideAuditDataFor(Command $command)
    {
        return $command->metadata()->all();
    }
}
