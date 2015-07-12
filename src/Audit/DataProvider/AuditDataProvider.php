<?php

namespace RayRutjes\DomainFoundation\Audit\DataProvider;

use RayRutjes\DomainFoundation\Command\Command;

interface AuditDataProvider
{
    /**
     * @param Command $command
     *
     * @return array
     */
    public function provideAuditDataFor(Command $command);
}
