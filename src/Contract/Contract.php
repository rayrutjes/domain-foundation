<?php

namespace RayRutjes\DomainFoundation\Contract;

interface Contract
{
    /**
     * @return string
     */
    public function className();

    /**
     * @return string
     */
    public function toString();

    /**
     * @return string
     */
    public function __toString();

    /**
     * @param Contract $contract
     *
     * @return bool
     */
    public function equals(Contract $contract);
}
