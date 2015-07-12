<?php

namespace RayRutjes\DomainFoundation\Audit\Logger;

use Psr\Log\LoggerInterface;
use RayRutjes\DomainFoundation\Command\Command;
use RayRutjes\DomainFoundation\Domain\Event\Event;

class SimpleAuditLogger implements AuditLogger
{
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @param LoggerInterface $logger
     */
    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * @param Command $command
     * @param mixed   $result
     * @param Event   ...$events
     */
    public function logSuccessful(Command $command, $result, ...$events)
    {
        $this->logger->info(
            'Command executed with success.',
            [
                'command' => $command,
                'result'  => $result,
                'events'  => $events
            ]
        );
    }

    /**
     * @param Command    $command
     * @param \Exception $cause
     * @param Event      ...$events
     */
    public function logFailure(Command $command, \Exception $cause, ...$events)
    {
        $this->logger->warning(
            'Command execution failed.',
            [
                'command' => $command,
                'cause'   => $cause,
                'events'  => $events
            ]
        );
    }
}
