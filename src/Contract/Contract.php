<?php

namespace RayRutjes\DomainFoundation\Contract;

final class Contract
{
    /**
     * @var string
     */
    private $className;

    /**
     * @param string $className
     */
    private function __construct($className)
    {
        if (!is_string($className)) {
            throw new \InvalidArgumentException('The class name should be a string.');
        }

        $this->className = $className;
    }

    /**
     * @param string $className
     *
     * @return Contract
     */
    public static function createFromClassName($className)
    {
        return new self($className);
    }

    /**
     * @param object $object
     *
     * @return Contract
     */
    public static function createFromObject($object)
    {
        if (!is_object($object)) {
            throw new \InvalidArgumentException('Object was expected.');
        }

        $className = get_class($object);

        return new self($className);
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->toString();
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
    public function className()
    {
        return $this->className;
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
