<?php

namespace RayRutjes\DomainFoundation\Contract;

final class ConventionalContractFactory implements ContractFactory
{
    /**
     * @param string $className
     *
     * @return Contract
     */
    public function createFromClassName($className)
    {
        return new ConventionalContract($className);
    }

    /**
     * @param object $object
     *
     * @return Contract
     */
    public function createFromObject($object)
    {
        if (!is_object($object)) {
            throw new \InvalidArgumentException('The object should be an object.');
        }

        $className = get_class($object);

        return new ConventionalContract($className);
    }
}
