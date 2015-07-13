<?php

namespace RayRutjes\DomainFoundation\Test\Unit\Message;

use RayRutjes\DomainFoundation\Message\MessageIdentifier;
use Rhumsaa\Uuid\Uuid;

class MessageIdentifierTest extends \PHPUnit_Framework_TestCase
{
    public function testCanBeConstructedWithAUuidString()
    {
        $messageIdentifier = new MessageIdentifier(Uuid::NIL);

        return $messageIdentifier;
    }

    public function testCanBeConstructedWithAUuid()
    {
        new MessageIdentifier(\RayRutjes\DomainFoundation\ValueObject\Identity\Uuid::generate());
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testEnsuresThatTheUuidIsAString()
    {
        new MessageIdentifier(3);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testEnsuresThatTheUuidIsValid()
    {
        new MessageIdentifier('notavaliduuid');
    }

    /**
     * @depends testCanBeConstructedWithAUuidString
     */
    public function testCanBeTranslatedIntoAString(MessageIdentifier $messageIdentifier)
    {
        $this->assertEquals(Uuid::NIL, $messageIdentifier->toString());
    }

    public function testCanBeRandomlyAndUniquelyGenerated()
    {
        $messageidentifier1 = MessageIdentifier::generate();
        $messageidentifier2 = MessageIdentifier::generate();

        $this->assertNotEquals($messageidentifier1, $messageidentifier2);
    }

    public function testCanConfirmItsEqualityWithAnotherMessageIdentifier()
    {
        $messageIdentifier1 = new MessageIdentifier(Uuid::NIL);
        $messageIdentifier2 = new MessageIdentifier(Uuid::NIL);

        $this->assertTrue($messageIdentifier1->equals($messageIdentifier2));
    }

    public function testCanConfirmItsInequalityWithAnotherMessageIdentifier()
    {
        $messageIdentifier1 = MessageIdentifier::generate();
        $messageIdentifier2 = MessageIdentifier::generate();

        $this->assertFalse($messageIdentifier1->equals($messageIdentifier2));
    }
}
