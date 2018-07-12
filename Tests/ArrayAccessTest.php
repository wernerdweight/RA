<?php
declare(strict_types=1);

namespace WernerDweight\RA\Tests;

use PHPUnit\Framework\TestCase;
use WernerDweight\RA\Exception\RAException;
use WernerDweight\RA\RA;

class ArrayAccessTest extends TestCase
{
    public function testToArray(): void
    {
        $regularRa = new RA(['a' => 'test1', 'b' => 'test2', 'c' => ['test3', 'test4', ['test5', 'test6']]], RA::REGULAR);
        $recursiveRa = new RA(['a' => 'test1', 'b' => 'test2', 'c' => ['test3', 'test4', ['test5', 'test6']]], RA::RECURSIVE);

        $this->assertInternalType('array', $regularRa['c']);
        $this->assertSame(RA::class, get_class($recursiveRa['c']));
        $this->assertSame(
            ['a' => 'test1', 'b' => 'test2', 'c' => ['test3', 'test4', ['test5', 'test6']]],
            $regularRa->toArray()
        );
        $this->assertNotSame(
            ['a' => 'test1', 'b' => 'test2', 'c' => ['test3', 'test4', ['test5', 'test6']]],
            $recursiveRa->toArray()
        );
        $this->assertSame(
            ['a' => 'test1', 'b' => 'test2', 'c' => ['test3', 'test4', ['test5', 'test6']]],
            $recursiveRa->toArray(RA::RECURSIVE)
        );

        $keys = [];
        $values = [];
        foreach ($regularRa as $key => $value) {
            $keys[] = $key;
            $values[] = $value;
        }
        $this->assertSame(['a', 'b', 'c'], $keys);
        $this->assertSame(['test1', 'test2', ['test3', 'test4', ['test5', 'test6']]], $values);

        $this->assertSame('test6', $regularRa['c'][2][1]);
        $this->assertSame('test6', $recursiveRa['c'][2][1]);

        $this->expectException(RAException::class);
        $this->expectExceptionCode(RAException::INVALID_OFFSET);
        $this->assertSame('test6', $recursiveRa['c'][2][2]);
    }

    public function testOffsetExists(): void
    {
        $ra = new RA(['a' => 'test']);
        $this->assertTrue($ra->offsetExists('a'));
        $this->assertFalse($ra->offsetExists('b'));
        $this->assertTrue($ra->keyExists('a'));
        $this->assertFalse($ra->keyExists('b'));
        $this->assertTrue($ra->hasKey('a'));
        $this->assertFalse($ra->hasKey('b'));
    }

    public function testOffsetGet(): void
    {
        $ra = new RA(['a' => 'test']);
        $this->assertSame('test', $ra->offsetGet('a'));
        $this->assertSame('test', $ra->get('a'));
        $this->expectException(RAException::class);
        $this->expectExceptionCode(RAException::INVALID_OFFSET);
        $ra->offsetGet('b');
    }

    public function testOffsetSet(): void
    {
        $ra = new RA();
        $ra->offsetSet('a', 'test');
        $this->assertTrue($ra->offsetExists('a'));
        $this->assertSame('test', $ra->offsetGet('a'));
        $ra->set('b', 'test');
        $this->assertTrue($ra->offsetExists('b'));
        $this->assertSame('test', $ra->offsetGet('b'));
    }

    public function testOffsetUnset(): void
    {
        $ra = new RA(['a' => 'test', 'b' => 'test']);
        $this->assertTrue($ra->offsetExists('a'));
        $this->assertTrue($ra->offsetExists('b'));
        $ra->offsetUnset('a');
        $ra->unset('b');
        $this->assertFalse($ra->offsetExists('a'));
        $this->assertFalse($ra->offsetExists('b'));
        $this->expectException(RAException::class);
        $this->expectExceptionCode(RAException::INVALID_OFFSET);
        $ra->offsetGet('a');
    }
}
