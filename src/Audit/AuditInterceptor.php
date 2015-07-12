<?php

namespace RayRutjes\DomainFoundation\Audit;

use RayRutjes\DomainFoundation\Audit\DataProvider\AuditDataProvider;
use RayRutjes\DomainFoundation\Audit\Logger\AuditLogger;
use RayRutjes\DomainFoundation\Command\Command;
use RayRutjes\DomainFoundation\Command\Interceptor\CommandHandlerInterceptor;
use RayRutjes\DomainFoundation\Command\Interceptor\InterceptorChain;
use RayRutjes\DomainFoundation\UnitOfWork\UnitOfWork;

class AuditInterceptor implements CommandHandlerInterceptor
{
    /**
     * @var AuditDataProvider
     */
    private $auditDataProvider;

    /**
     * @var AuditLogger
     */
    private $auditLogger;

    /**
     * @param AuditDataProvider $auditDataProvider
     * @param AuditLogger       $auditLogger
     */
    public function __construct(AuditDataProvider $auditDataProvider, AuditLogger $auditLogger)
    {
        $this->auditDataProvider = $auditDataProvider;
        $this->auditLogger = $auditLogger;
    }

    /**
     * @param Command          $command
     * @param UnitOfWork       $unitOfWork
     * @param InterceptorChain $interceptorChain
     *
     * @return mixed The result of the command handler, if any.
     */
    public function handle(Command $command, UnitOfWork $unitOfWork, InterceptorChain $interceptorChain)
    {
        $auditListener = new AuditUnitOfWorkListener($command, $this->auditDataProvider, $this->auditLogger);
        $unitOfWork->registerListener($auditListener);

        $result = $interceptorChain->proceed();
        $auditListener->setResult($result);

        return $result;
    }
}
