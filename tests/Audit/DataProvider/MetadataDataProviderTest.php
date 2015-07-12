<?php

namespace RayRutjes\DomainFoundation\Test\Audit\DataProvider;

use RayRutjes\DomainFoundation\Audit\DataProvider\MetadataDataProvider;
use RayRutjes\DomainFoundation\Command\GenericCommand;
use RayRutjes\DomainFoundation\Message\MessageIdentifier;
use RayRutjes\DomainFoundation\Message\Metadata;
use RayRutjes\DomainFoundation\Test\Persistence\Pdo\EventStore\PayloadStub;

class MetadataDataProviderTest extends \PHPUnit_Framework_TestCase
{
    public function testExtractsMetadataOfACommand()
    {
        $data = ['somedata' => 'data'];
        $command = new GenericCommand(MessageIdentifier::generate(), new PayloadStub(), new Metadata($data));
        $metadataProvider = new MetadataDataProvider();
        $this->assertEquals($data, $metadataProvider->provideAuditDataFor($command));
    }
}
