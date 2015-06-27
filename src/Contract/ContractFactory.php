<?php

namespace RayRutjes\DomainFoundation\Contract;

interface ContractFactory
{
    /**
     * @param string $className
     *
     * @return Contract
     */
    public function createFromClassName($className);

    /**
     * @param object $object
     *
     * @return Contract
     */
    public function createFromObject($object);
}
