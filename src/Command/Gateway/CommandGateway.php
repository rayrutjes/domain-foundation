<?php

namespace RayRutjes\DomainFoundation\Command\Gateway;

interface CommandGateway
{
    /**
     * @param mixed $command
     */
    public function send($command);

    /**
     * @param mixed $command
     * @param int   $timeout Timeout in milliseconds.
     *
     * @return mixed
     */
    public function sendAndWait($command, $timeout = 300);
}
