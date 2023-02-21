<?php
declare(strict_types=1);

namespace WernerDweight\RA\Tests;

use PHPUnit\Framework\TestCase;
use WernerDweight\RA\RA;

class CountableTest extends TestCase
{
    public function testCount(): void
    {
        $regularRa = new RA([
            'a' => 'test1',
            'b' => 'test2',
            'c' => ['test3', 'test4', 'test5', ['test6', 'test7']],
        ], RA::REGULAR);
        $recursiveRa = new RA([
            'a' => 'test1',
            'b' => 'test2',
            'c' => ['test3', 'test4', 'test5', ['test6', 'test7']],
        ], RA::RECURSIVE);

        $this->assertSame(3, $regularRa->count());
        $this->assertSame(3, $regularRa->size());
        $this->assertSame(3, $regularRa->length());
        $this->assertSame(3, count($regularRa));

        /** @var array<int, string|string[]> $c */
        $c = $regularRa['c'];
        $this->assertSame(4, count($c));

        /** @var array<int, string> $c3 */
        $c3 = $c[3];
        $this->assertSame(2, count($c3));

        $this->assertSame(3, $recursiveRa->count());
        $this->assertSame(3, $recursiveRa->size());
        $this->assertSame(3, $recursiveRa->length());
        $this->assertSame(3, count($recursiveRa));

        /** @var RA<int, string|RA<int, string>> $rc */
        $rc = $recursiveRa['c'];
        $this->assertSame(4, $rc->count());
        $this->assertSame(4, $rc->size());
        $this->assertSame(4, $rc->length());
        $this->assertSame(4, count($rc));

        /** @var RA<int, string[]> $rc3 */
        $rc3 = $rc[3];
        $this->assertSame(2, $rc3->count());
        $this->assertSame(2, $rc3->size());
        $this->assertSame(2, $rc3->length());
        $this->assertSame(2, count($rc3));
    }
}
