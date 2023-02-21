<?php
declare(strict_types=1);

namespace WernerDweight\RA\Tests;

use PHPUnit\Framework\TestCase;
use WernerDweight\RA\Exception\RAException;
use WernerDweight\RA\RA;

class HelpersTest extends TestCase
{
    public function testHelpers(): void
    {
        $ra = new RA([
            'int' => 123,
            'float' => 123.456,
            'string' => 'A',
        ], RA::REGULAR);

        $ra->increment('int');
        $ra->increment('float');

        $this->assertSame(124, $ra->getInt('int'));
        $this->assertSame(124.456, $ra->getFloat('float'));

        $ra->decrement('int');
        $ra->decrement('float');

        $this->assertSame(123, $ra->getInt('int'));
        $this->assertSame(123.456, $ra->getFloat('float'));

        $this->expectException(RAException::class);
        $ra->decrement('string');
    }
}
