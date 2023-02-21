<?php
declare(strict_types=1);

namespace WernerDweight\RA\Tests;

use PHPUnit\Framework\TestCase;
use WernerDweight\RA\Exception\RAException;
use WernerDweight\RA\RA;

class PresenceTest extends TestCase
{
    public function testPresence(): void
    {
        $ra1 = new RA(['a']);

        $this->assertTrue(isset($ra1[0]));
        $this->assertFalse(isset($ra1[1]));

        $ra2 = new RA([
            'a' => 'test',
        ]);
        $this->assertFalse(isset($ra2[0]));
        $this->assertTrue(isset($ra2['a']));
        $this->assertFalse(isset($ra2['b']));

        $this->expectException(RAException::class);
        $this->expectExceptionCode(RAException::INVALID_OFFSET);
        echo null === $ra2['b'];
    }

    public function testPresence2(): void
    {
        $ra = new RA([
            'a' => 'test',
        ]);

        unset($ra['a']);
        $this->expectException(RAException::class);
        $this->expectExceptionCode(RAException::INVALID_OFFSET);
        echo null === $ra['a'];
    }

    public function testPresence3(): void
    {
        $ra = new RA([
            'a' => 'test',
        ]);

        unset($ra['a']);
        $this->expectException(RAException::class);
        $this->expectExceptionCode(RAException::INVALID_OFFSET);
        unset($ra['a']);
    }

    public function testPresence4(): void
    {
        $ra = new RA([
            'a' => 'test',
        ]);

        $ra['b'] = 'test2';
        $this->assertSame('test2', $ra['b']);
    }
}
