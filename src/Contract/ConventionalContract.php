<?php

namespace RayRutjes\DomainFoundation\Contract;

final class ConventionalContract implements Contract
{
    /**
     * @var string
     */
    private $className;

    /**
     * @param string $className
     */
    public function __construct($className)
    {
        if (!is_string($className)) {
            throw new \InvalidArgumentException('The class name should be a string.');
        }

        $this->className = $className;
    }

    /**
     * @return string
     */
    public function className()
    {
        return $this->className;
    }

    /**
     * @return string
     */
    public function toString()
    {
        return $this->className();
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->toString();
    }

    /**
     * @param Contract $contract
     *
     * @return bool
     */
    public function equals(Contract $contract)
    {
        return $contract->toString() === $this->toString();
    }
}
