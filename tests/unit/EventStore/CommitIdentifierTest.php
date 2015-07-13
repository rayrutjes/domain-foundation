<?php

namespace RayRutjes\DomainFoundation\Test\Unit\Commit;

use RayRutjes\DomainFoundation\EventStore\CommitIdentifier;
use Rhumsaa\Uuid\Uuid;

class CommitIdentifierTest extends \PHPUnit_Framework_TestCase
{
    public function testCanBeConstructedWithAUuidString()
    {
        $commitIdentifier = new CommitIdentifier(Uuid::NIL);

        return $commitIdentifier;
    }

    public function testCanBeConstructedWithAUuid()
    {
        new CommitIdentifier(\RayRutjes\DomainFoundation\ValueObject\Identity\Uuid::generate());
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testEnsuresThatTheUuidIsAString()
    {
        new CommitIdentifier(3);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testEnsuresThatTheUuidIsValid()
    {
        new CommitIdentifier('notavaliduuid');
    }

    /**
     * @depends testCanBeConstructedWithAUuidString
     */
    public function testCanBeTranslatedIntoAString(CommitIdentifier $commitIdentifier)
    {
        $this->assertEquals(Uuid::NIL, $commitIdentifier->toString());
    }

    public function testCanBeRandomlyAndUniquelyGenerated()
    {
        $messageidentifier1 = CommitIdentifier::generate();
        $messageidentifier2 = CommitIdentifier::generate();

        $this->assertNotEquals($messageidentifier1, $messageidentifier2);
    }

    public function testCanConfirmItsEqualityWithAnotherCommitIdentifier()
    {
        $commitIdentifier1 = new CommitIdentifier(Uuid::NIL);
        $commitIdentifier2 = new CommitIdentifier(Uuid::NIL);

        $this->assertTrue($commitIdentifier1->equals($commitIdentifier2));
    }

    public function testCanConfirmItsInequalityWithAnotherCommitIdentifier()
    {
        $commitIdentifier1 = CommitIdentifier::generate();
        $commitIdentifier2 = CommitIdentifier::generate();

        $this->assertFalse($commitIdentifier1->equals($commitIdentifier2));
    }
}
