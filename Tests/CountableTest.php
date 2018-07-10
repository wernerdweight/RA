<?php
declare(strict_types=1);

namespace Tests;

use PHPUnit\Framework\TestCase;
use WernerDweight\RA\RA;

class CountableTest extends TestCase
{
    public function testCount(): void
    {
        $regularRa = new RA(['a' => 'test1', 'b' => 'test2', 'c' => ['test3', 'test4', 'test5', ['test6', 'test7']]], RA::REGULAR);
        $recursiveRa = new RA(['a' => 'test1', 'b' => 'test2', 'c' => ['test3', 'test4', 'test5', ['test6', 'test7']]], RA::RECURSIVE);

        $this->assertSame(3, $regularRa->count());
        $this->assertSame(3, $regularRa->size());
        $this->assertSame(3, $regularRa->length());
        $this->assertSame(3, count($regularRa));

        $this->assertSame(4, count($regularRa['c']));

        $this->assertSame(2, count($regularRa['c'][3]));

        $this->assertSame(3, $recursiveRa->count());
        $this->assertSame(3, $recursiveRa->size());
        $this->assertSame(3, $recursiveRa->length());
        $this->assertSame(3, count($recursiveRa));

        $this->assertSame(4, $recursiveRa['c']->count());
        $this->assertSame(4, $recursiveRa['c']->size());
        $this->assertSame(4, $recursiveRa['c']->length());
        $this->assertSame(4, count($recursiveRa['c']));

        $this->assertSame(2, $recursiveRa['c'][3]->count());
        $this->assertSame(2, $recursiveRa['c'][3]->size());
        $this->assertSame(2, $recursiveRa['c'][3]->length());
        $this->assertSame(2, count($recursiveRa['c'][3]));
    }
}
