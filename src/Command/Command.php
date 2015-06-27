<?php

namespace RayRutjes\DomainFoundation\Command;

use RayRutjes\DomainFoundation\Message\Message;

interface Command extends Message
{
    /**
     * Return the Command's name.
     *
     * @return string
     */
    public function commandName();
}
