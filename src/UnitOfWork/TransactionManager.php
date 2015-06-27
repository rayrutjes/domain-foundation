<?php

namespace RayRutjes\DomainFoundation\UnitOfWork;

interface TransactionManager
{
    public function startTransaction();

    public function commitTransaction();

    public function rollbackTransaction();
}
