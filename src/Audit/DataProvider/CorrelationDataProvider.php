<?php

namespace RayRutjes\DomainFoundation\Audit\DataProvider;

use RayRutjes\DomainFoundation\Command\Command;

class CorrelationDataProvider implements AuditDataProvider
{
    /**
     * @param Command $command
     *
     * @return array
     */
    public function provideAuditDataFor(Command $command)
    {
        return [
            'command_name'       => $command->commandName(),
            'command_identifier' => $command->identifier()->toString(),
        ];
    }
}
