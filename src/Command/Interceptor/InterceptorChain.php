<?php

namespace RayRutjes\DomainFoundation\Command\Interceptor;

interface InterceptorChain
{
    /**
     * @return mixed the result of the command handler, if any.
     */
    public function proceed();
}
