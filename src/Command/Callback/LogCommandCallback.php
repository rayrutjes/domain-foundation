<?php

namespace RayRutjes\DomainFoundation\Command\Callback;

use Psr\Log\LoggerInterface;
use RayRutjes\DomainFoundation\Command\Command;

class LogCommandCallback implements CommandCallback
{
    /**
     * @var Command
     */
    private $command;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @param Command         $command
     * @param LoggerInterface $logger
     */
    public function __construct(Command $command, LoggerInterface $logger)
    {
        $this->command = $command;
        $this->logger = $logger;
    }

    /**
     * @param $result
     */
    public function onSuccess($result = null)
    {
        $this->logger->info('Command executed successfully', ['commandName' => $this->command->commandName()]);
    }

    /**
     * @param \Exception $cause
     */
    public function onFailure(\Exception $cause)
    {
        $this->logger->warning('Command execution failed', ['commandName' => $this->command->commandName(), 'cause' => $cause]);
    }
}
