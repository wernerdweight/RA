<?php
declare(strict_types=1);

namespace WernerDweight\RA\Tests;

use PHPUnit\Framework\TestCase;
use WernerDweight\RA\RA;

class TypedGettersTest extends TestCase
{
    public function testTypedGetters(): void
    {
        $ra = new RA([
            'bool' => true,
            'int' => 123,
            'float' => 123.456,
            'string' => 'test',
            'array' => ['test1', 'test2', ['test3', 'test4']],
            'RA' => new RA(['test1', 'test2', ['test3', 'test4']]),
            'callable' => function (): string {
                return '';
            },
            'callable2' => 'strtoupper',
            'iterable' => new RA(),
            'iterable2' => [],
        ], RA::REGULAR);

        $this->assertTrue($ra->getBool('bool'));
        $this->assertSame(123, $ra->getInt('int'));
        $this->assertSame(123.456, $ra->getFloat('float'));
        $this->assertSame('test', $ra->getString('string'));
        $this->assertSame(['test1', 'test2', ['test3', 'test4']], $ra->getArray('array'));
        $this->assertInstanceOf(RA::class, $ra->getRA('RA'));
        $this->assertTrue(is_callable($ra->getCallable('callable')));
        $this->assertTrue(is_callable($ra->getCallable('callable2')));
        $this->assertTrue(is_iterable($ra->getIterable('iterable')));
        $this->assertTrue(is_iterable($ra->getIterable('iterable2')));

        $this->expectException(\TypeError::class);
        $ra->getInt('array');
    }
}
