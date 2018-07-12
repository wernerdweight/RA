<?php
declare(strict_types=1);

namespace WernerDweight\RA\Tests;

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
        $this->assertNull($ra->getCurrentIndex());
        $this->assertNull($ra->getCurrentKey());
        $this->assertFalse($ra->current());
        $this->assertFalse($ra->pos());
        $this->assertFalse($ra->get());

        $ra->rewind();
        $this->assertSame('a', $ra->key());
        $this->assertSame('a', $ra->getCurrentIndex());
        $this->assertSame('a', $ra->getCurrentKey());
        $this->assertSame('test1', $ra->current());
        $this->assertSame('test1', $ra->pos());
        $this->assertSame('test1', $ra->get());

        $ra->end();
        $this->assertSame('test3', $ra->prev());
        $this->assertSame('test2', $ra->previous());
        $this->assertSame('test1', $ra->reset());
        $this->assertSame('test4', $ra->last());
        $this->assertSame('test1', $ra->first());
    }
}
