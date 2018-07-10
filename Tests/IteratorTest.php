<?php
declare(strict_types=1);

namespace Tests;

use PHPUnit\Framework\TestCase;
use WernerDweight\RA\RA;

class IteratorTest extends TestCase
{
    public function testIterator(): void
    {
        $ra = new RA(['a' => 'test1', 'b' => 'test2', 'c' => 'test3', 'd' => 'test4']);

        $keys = [];
        $values = [];
        while ($ra->valid()) {
            $keys[] = $ra->key();
            $values[] = $ra->current();
            $ra->next();
        }
        $this->assertSame(['a', 'b', 'c', 'd'], $keys);
        $this->assertSame(['test1', 'test2', 'test3', 'test4'], $values);
        $this->assertFalse($ra->valid());
        $this->assertNull($ra->key());
        $this->assertFalse($ra->current());

        $ra->rewind();
        $this->assertSame('a', $ra->key());
        $this->assertSame('test1', $ra->current());
    }
}
