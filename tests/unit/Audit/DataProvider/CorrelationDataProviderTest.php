<?php

namespace RayRutjes\DomainFoundation\Test\Unit\Audit\DataProvider;

use RayRutjes\DomainFoundation\Audit\DataProvider\CorrelationDataProvider;
use RayRutjes\DomainFoundation\Command\GenericCommand;
use RayRutjes\DomainFoundation\Message\MessageIdentifier;
use RayRutjes\DomainFoundation\Test\Unit\Message\PayloadStub;

class CorrelationDataProviderTest extends \PHPUnit_Framework_TestCase
{
    public function testExtractsCorrelationDataOutOfACommand()
    {
        $commandIdentifier = MessageIdentifier::generate();
        $payload = new PayloadStub();
        $data = [
            'command_name'       => get_class($payload),
            'command_identifier' => $commandIdentifier->toString(),
        ];

        $command = new GenericCommand($commandIdentifier, $payload);
        $correlationDataProvider = new CorrelationDataProvider();
        $this->assertEquals($data, $correlationDataProvider->provideAuditDataFor($command));
    }
}
