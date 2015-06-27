<?php

namespace RayRutjes\DomainFoundation\Command\Callback;

interface CommandCallback
{
    /**
     * @param $result
     */
    public function onSuccess($result = null);

    /**
     * @param \Exception $cause
     */
    public function onFailure(\Exception $cause);
}
