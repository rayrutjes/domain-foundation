<?php

namespace RayRutjes\DomainFoundation\Command\Callback;

use Monolog\Logger;
use RayRutjes\DomainFoundation\Command\Command;

class LogCommandCallback implements CommandCallback
{
    /**
     * @var Command
     */
    private $command;

    /**
     * @var Logger
     */
    private $logger;

    /**
     * @param Command $command
     * @param Logger  $logger
     */
    public function __construct(Command $command, Logger $logger)
    {
        $this->command = $command;
        $this->logger = $logger;
    }

    /**
     * @param $result
     */
    public function onSuccess($result = null)
    {
        $this->logger->addInfo('Command executed successfully', ['commandName' => $this->command->commandName()]);
    }

    /**
     * @param \Exception $cause
     */
    public function onFailure(\Exception $cause)
    {
        $this->logger->addWarning('Command execution failed', ['commandName' => $this->command->commandName(), 'cause' => $cause]);
    }
}
