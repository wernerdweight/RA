<?php
declare(strict_types=1);

namespace WernerDweight\RA\Tests;

use PHPUnit\Framework\TestCase;
use WernerDweight\RA\Exception\RAException;
use WernerDweight\RA\RA;

class ArrayMethodsTest extends TestCase
{
    public function testPush(): void
    {
        $ra = new RA();
        $this->assertSame(0, $ra->length());
        $ra->push('test1');
        $this->assertSame(1, $ra->length());
        $ra->push('test2', 'test3');
        $this->assertSame(3, $ra->length());
        $this->assertSame('test1', $ra[0]);
        $this->assertSame('test2', $ra[1]);
        $this->assertSame('test3', $ra[2]);
    }

    public function testPop(): void
    {
        $ra = new RA(['test1', 'test2', 'test3']);
        $this->assertSame(3, $ra->length());
        $this->assertSame('test3', $ra->pop());
        $this->assertSame(2, $ra->length());
        $this->expectException(RAException::class);
        $this->expectExceptionCode(RAException::INVALID_OFFSET);
        $ra->offsetGet(2);
    }

    public function testChangeKeyCase(): void
    {
        $ra = new RA(['a' => 'test1', 'b' => 'test2', 'c' => 'test3']);
        $this->assertSame(['a', 'b', 'c'], $ra->keys()->toArray());
        $ra2 = $ra->changeKeyCase(CASE_UPPER);
        $this->assertSame(['a', 'b', 'c'], $ra->keys()->toArray());
        $this->assertNotSame(['a', 'b', 'c'], $ra2->keys()->toArray());
        $this->assertSame(['A', 'B', 'C'], $ra2->keys()->toArray());
    }

    public function testChunk(): void
    {
        $ra = new RA(['a' => 'test1', 'b' => 'test2', 'c' => 'test3', 'd' => 'test4', 'e' => 'test5']);

        $chunks = $ra->chunk(2);
        $this->assertSame(5, $ra->length());
        $this->assertSame(3, $chunks->length());
        $this->assertSame(2, $chunks[0]->length());
        $this->assertSame(2, $chunks[1]->length());
        $this->assertSame(1, $chunks[2]->length());

        $associativeChunks = $ra->chunk(2, true);
        $this->assertSame(5, $ra->length());
        $this->assertSame([0, 1, 2], $associativeChunks->keys()->toArray());
        $this->assertSame(['a', 'b'], $associativeChunks[0]->keys()->toArray());
        $this->assertSame(['c', 'd'], $associativeChunks[1]->keys()->toArray());
        $this->assertSame(['e'], $associativeChunks[2]->keys()->toArray());
    }

    public function testColumn(): void
    {
        $ra = new RA([
            ['id' => 'abcd', 'title' => 'Test 1', 'price' => 123],
            ['id' => 'efgh', 'title' => 'Test 2', 'price' => 0],
            ['id' => 'ijkl', 'title' => 'Test 3', 'price' => 345]
        ]);

        $titleById = $ra->column('title', 'id');
        $priceByTitle = $ra->column('price', 'title');
        $this->assertSame(['abcd' => 'Test 1', 'efgh' => 'Test 2', 'ijkl' => 'Test 3'], $titleById->toArray());
        $this->assertSame(['Test 1' => 123, 'Test 2' => 0, 'Test 3' => 345], $priceByTitle->toArray());
    }

    public function testCombine(): void
    {
    }

    public function testCountValues(): void
    {
    }

    public function testDiffAssoc(): void
    {
    }

    public function testDiffKey(): void
    {
    }

    public function testDiffUassoc(): void
    {
    }

    public function testDiffUkey(): void
    {
    }

    public function testDiff(): void
    {
    }

    public function testFillKeys(): void
    {
    }

    public function testFill(): void
    {
    }

    public function testFilter(): void
    {
    }

    public function testFlip(): void
    {
    }

    public function testIntersectAssoc(): void
    {
    }

    public function testIntersectKey(): void
    {
    }

    public function testIntersectUassoc(): void
    {
    }

    public function testIntersectUkey(): void
    {
    }

    public function testIntersect(): void
    {
    }

    public function testKeys(): void
    {
    }

    public function testMap(): void
    {
    }

    public function testMergeRecursive(): void
    {
    }

    public function testMerge(): void
    {
    }

    public function testPad(): void
    {
    }

    public function testProduct(): void
    {
    }

    public function testRandom(): void
    {
    }

    public function testReduce(): void
    {
    }

    public function testReplaceRecursive(): void
    {
    }

    public function testReplace(): void
    {
    }

    public function testReverse(): void
    {
    }

    public function testSearch(): void
    {
    }

    public function testShift(): void
    {
    }

    public function testSlice(): void
    {
    }

    public function testSplice(): void
    {
    }

    public function testSum(): void
    {
    }

    public function testUdiffAssoc(): void
    {
    }

    public function testUdiffUassoc(): void
    {
    }

    public function testUdiff(): void
    {
    }

    public function testUintersectAssoc(): void
    {
    }

    public function testUintersectUassoc(): void
    {
    }

    public function testUintersect(): void
    {
    }

    public function testUnique(): void
    {
    }

    public function testUnshift(): void
    {
    }

    public function testValues(): void
    {
    }

    public function testWalkRecursive(): void
    {
    }

    public function testWalk(): void
    {
    }

    public function testArsort(): void
    {
    }

    public function testAsort(): void
    {
    }

    public function testEnd(): void
    {
    }

    public function testContains(): void
    {
    }

    public function testKrsort(): void
    {
    }

    public function testKsort(): void
    {
    }

    public function testNatcasesort(): void
    {
    }

    public function testNatsort(): void
    {
    }

    public function testPrev(): void
    {
    }

    public function testRange(): void
    {
    }

    public function testReset(): void
    {
    }

    public function testRsort(): void
    {
    }

    public function testShuffle(): void
    {
    }

    public function testSort(): void
    {
    }

    public function testUasort(): void
    {
    }

    public function testUksort(): void
    {
    }

    public function testUsort(): void
    {
    }
}
