<?php

namespace RayRutjes\DomainFoundation\Test\Unit\ValueObject\Identity;

use RayRutjes\DomainFoundation\ValueObject\Identity\Uuid;

class UuidTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testShouldEnsureItIsConstructedWithAString()
    {
        new Uuid(3);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testShouldEnsureItIsConstructedWithAValidUuid()
    {
        new Uuid('Not an UUID');
    }

    public function testCanCheckIfItIsEqualToAnotherUuid()
    {
        $uuid1 = $this->nilUuid();
        $uuid2 = $this->nilUuid();

        $this->assertTrue($uuid1->equals($uuid2));
    }

    /**
     * @depends testCanCheckIfItIsEqualToAnotherUuid
     */
    public function testCanBeGeneratedRandomlyAndUniquely()
    {
        $uuid1 = Uuid::generate();
        $uuid2 = Uuid::generate();

        $this->assertFalse($uuid1->equals($uuid2));
    }

    public function testCanBeTranslatedIntoAString()
    {
        $uuid = $this->nilUuid();

        $this->assertEquals(\Rhumsaa\Uuid\Uuid::NIL, $uuid->toString());
    }

    public function testCanBeCastedAsAString()
    {
        $uuid = $this->nilUuid();

        $this->assertEquals(\Rhumsaa\Uuid\Uuid::NIL, (string) $uuid);
    }

    public function nilUuid()
    {
        return new Uuid(\Rhumsaa\Uuid\Uuid::NIL);
    }
}
