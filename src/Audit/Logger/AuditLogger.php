<?php

namespace RayRutjes\DomainFoundation\Audit\Logger;

use RayRutjes\DomainFoundation\Command\Command;
use RayRutjes\DomainFoundation\Domain\Event\Event;

interface AuditLogger
{
    /**
     * @param Command $command
     * @param mixed   $result
     * @param Event   ...$events
     */
    public function logSuccessful(Command $command, $result, ...$events);

    /**
     * @param Command    $command
     * @param \Exception $cause
     * @param Event      ...$events
     */
    public function logFailure(Command $command, \Exception $cause, ...$events);
}
