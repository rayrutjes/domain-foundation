<?php

namespace RayRutjes\DomainFoundation\EventBus\Listener;

interface ReplayAware
{
    /**
     * Called before the replay starts. Gives you the opportunity to truncate
     * database tables for example.
     */
    public function beforeReplay();

    /**
     * Called after the replay has finished.
     */
    public function afterReplay();

    /**
     * @param \Exception $cause
     *
     * @return mixed
     */
    public function onReplayFailure(\Exception $cause);
}
