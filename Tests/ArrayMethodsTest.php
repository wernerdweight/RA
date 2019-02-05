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
        $ra->append('test2', 'test3');
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
        $ra = new RA([
            'a' => 'test1',
            'b' => 'test2',
            'c' => 'test3',
        ]);
        $this->assertSame(['a', 'b', 'c'], $ra->keys()->toArray());
        $ra2 = $ra->changeKeyCase(CASE_UPPER);
        $this->assertSame(['a', 'b', 'c'], $ra->keys()->toArray());
        $this->assertNotSame(['a', 'b', 'c'], $ra2->keys()->toArray());
        $this->assertSame(['A', 'B', 'C'], $ra2->keys()->toArray());
    }

    public function testChunk(): void
    {
        $ra = new RA([
            'a' => 'test1',
            'b' => 'test2',
            'c' => 'test3',
            'd' => 'test4',
            'e' => 'test5',
        ]);

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
            [
                'id' => 'abcd',
                'title' => 'Test 1',
                'price' => 123,
            ],
            [
                'id' => 'efgh',
                'title' => 'Test 2',
                'price' => 0,
            ],
            [
                'id' => 'ijkl',
                'title' => 'Test 3',
                'price' => 345,
            ],
        ]);

        $titleById = $ra->column('title', 'id');
        $priceByTitle = $ra->column('price', 'title');
        $this->assertSame([
            'abcd' => 'Test 1',
            'efgh' => 'Test 2',
            'ijkl' => 'Test 3',
        ], $titleById->toArray());
        $this->assertSame([
            'Test 1' => 123,
            'Test 2' => 0,
            'Test 3' => 345,
        ], $priceByTitle->toArray());
    }

    public function testCombine(): void
    {
        $keys = new RA(['a', 'b', 'c', 'd']);
        $values = new RA(['test1', 'test2', 'test3', 'test4']);
        $asKeys = $keys->combine($values, RA::AS_KEYS);
        $asValues = $keys->combine($values, RA::AS_VALUES);
        $this->assertSame([
            'a' => 'test1',
            'b' => 'test2',
            'c' => 'test3',
            'd' => 'test4',
        ], $asKeys->toArray());
        $this->assertSame([
            'test1' => 'a',
            'test2' => 'b',
            'test3' => 'c',
            'test4' => 'd',
        ], $asValues->toArray());
    }

    public function testCountValues(): void
    {
        $ra = new RA(['a', 'a', 'b', 'c', 'd', 'c']);
        $counted = $ra->countValues();
        $this->assertSame(4, $counted->length());
        $this->assertSame([
            'a' => 2,
            'b' => 1,
            'c' => 2,
            'd' => 1,
        ], $counted->toArray());
        $aggregated = $ra->aggregateValues();
        $this->assertSame(4, $aggregated->length());
        $this->assertSame([
            'a' => 2,
            'b' => 1,
            'c' => 2,
            'd' => 1,
        ], $aggregated->toArray());
    }

    public function testDiffAssoc(): void
    {
        $ra1 = new RA(['a', 'b', 'c', 'd']);
        $ra2 = new RA(['a', 'c']);
        $ra3 = new RA(['c', 'b', 'f']);
        $diff = $ra1->diffAssoc($ra2, $ra3);
        $this->assertSame([
            2 => 'c',
            3 => 'd',
        ], $diff->toArray());
    }

    public function testDiffKey(): void
    {
        $ra1 = new RA(['a', 'b', 'c', 'd']);
        $ra2 = new RA(['a', 'c']);
        $ra3 = new RA(['c', 'b', 'f']);
        $diff = $ra1->diffKey($ra2, $ra3);
        $this->assertSame([3 => 'd'], $diff->toArray());
    }

    public function testDiffUassoc(): void
    {
        $ra1 = new RA(['a', 'b', 'c', 'd']);
        $ra2 = new RA(['a', 'c']);
        $ra3 = new RA(['c', 'b', 'f']);
        $diff = $ra1->diffUassoc($ra2, $ra3, function ($key1, $key2) {
            return 1 !== $key1
                ? $key1 === $key2 ? 0 : 1
                : -1;
        });
        $this->assertSame([
            1 => 'b',
            2 => 'c',
            3 => 'd',
        ], $diff->toArray());
    }

    public function testDiffUkey(): void
    {
        $ra1 = new RA(['a', 'b', 'c', 'd']);
        $ra2 = new RA(['a', 'c']);
        $ra3 = new RA(['c', 'b', 'f']);
        $diff = $ra1->diffUkey($ra2, $ra3, function ($key1, $key2) {
            return $key1 < 2
                ? $key1 === $key2 ? 0 : 1
                : -1;
        });
        $this->assertSame([
            2 => 'c',
            3 => 'd',
        ], $diff->toArray());
    }

    public function testDiff(): void
    {
        $ra1 = new RA(['a', 'b', 'c', 'd']);
        $ra2 = new RA(['a', 'c']);
        $ra3 = new RA(['c', 'b', 'f']);
        $diff = $ra1->diff($ra2, $ra3);
        $this->assertSame([3 => 'd'], $diff->toArray());
    }

    public function testFillKeys(): void
    {
        $ra = new RA(['a', 'b', 'c', 'd']);
        $filled = $ra->fillKeys('test');
        $this->assertSame([
            'a' => 'test',
            'b' => 'test',
            'c' => 'test',
            'd' => 'test',
        ], $filled->toArray());
    }

    public function testFill(): void
    {
        $ra = new RA();
        $ra->fill(2, 4, 'test');
        $this->assertSame([
            2 => 'test',
            3 => 'test',
            4 => 'test',
            5 => 'test',
        ], $ra->toArray());
    }

    public function testFilter(): void
    {
        $ra = new RA(range(1, 10));
        $filtered = $ra->filter(function ($entry) {
            return 0 === $entry % 2;
        });
        $this->assertSame([
            1 => 2,
            3 => 4,
            5 => 6,
            7 => 8,
            9 => 10,
        ], $filtered->toArray());
    }

    public function testFlip(): void
    {
        $ra = new RA([
            'a' => 'test1',
            'b' => 'test2',
        ]);
        $flipped = $ra->flip();
        $this->assertSame([
            'test1' => 'a',
            'test2' => 'b',
        ], $flipped->toArray());
    }

    public function testIntersectAssoc(): void
    {
        $ra1 = new RA(['a', 'b', 'c', 'd']);
        $ra2 = new RA(['a', 'b']);
        $ra3 = new RA(['c', 'b', 'a']);
        $intersect = $ra1->intersectAssoc($ra2, $ra3);
        $this->assertSame([1 => 'b'], $intersect->toArray());
    }

    public function testIntersectKey(): void
    {
        $ra1 = new RA(['a', 'b', 'c', 'd']);
        $ra2 = new RA(['a', 'c']);
        $ra3 = new RA(['c', 'b', 'f']);
        $intersect = $ra1->intersectKey($ra2, $ra3);
        $this->assertSame([
            0 => 'a',
            1 => 'b',
        ], $intersect->toArray());
    }

    public function testIntersectUassoc(): void
    {
        $ra1 = new RA(['a', 'b', 'c', 'd']);
        $ra2 = new RA(['a', 'c']);
        $ra3 = new RA(['c', 'b', 'f']);
        $intersect = $ra1->intersectUassoc($ra2, $ra3, function ($key1, $key2) {
            return 1 !== $key1
                ? $key1 === $key2 ? 1 : 0
                : -1;
        });
        $this->assertSame([2 => 'c'], $intersect->toArray());
    }

    public function testIntersectUkey(): void
    {
        $ra1 = new RA(['a', 'b', 'c', 'd']);
        $ra2 = new RA(['a', 'c']);
        $ra3 = new RA(['c', 'b', 'f']);
        $intersect = $ra1->intersectUkey($ra2, $ra3, function ($key1, $key2) {
            return $key1 < 1
                ? $key1 === $key2 ? 0 : 1
                : -1;
        });
        $this->assertSame([0 => 'a'], $intersect->toArray());
    }

    public function testIntersect(): void
    {
        $ra1 = new RA(['a', 'b', 'c', 'd']);
        $ra2 = new RA(['d', 'b']);
        $ra3 = new RA(['c', 'a', 'b']);
        $intersect = $ra1->intersect($ra2, $ra3);
        $this->assertSame([1 => 'b'], $intersect->toArray());
    }

    public function testKeys(): void
    {
        $ra = new RA([
            'a' => 'test1',
            'b',
            'c' => 'test2',
            'd',
        ]);
        $keys1 = $ra->keys();
        $this->assertSame(['a', 0, 'c', 1], $keys1->toArray());
        $keys2 = $ra->getKeys();
        $this->assertSame(['a', 0, 'c', 1], $keys2->toArray());
    }

    public function testMap(): void
    {
        $ra = new RA(['a', 'b', 'c', 'd']);
        $mapped = $ra->map(function ($entry) {
            return strtoupper($entry);
        });
        $this->assertSame(['A', 'B', 'C', 'D'], $mapped->toArray());
    }

    public function testMergeRecursive(): void
    {
        $ra1 = new RA([
            'a' => ['aa' => [
                'aaa' => 1,
            'aab' => 2,
            ], [
                'a0a' => 1,
            'a0b' => 2,
            ]],
            'b' => 1,
            'c',
        ]);
        $ra2 = new RA([
            'a' => ['aa' => [
                'aab' => 3,
            'aac' => 3,
            ], [
                'a0a' => 2,
            'a0c' => 3,
            ]],
            'b' => ['bb' => 3],
            'd',
        ]);
        $merged1 = $ra1->mergeRecursive($ra2);
        $merged2 = $ra2->mergeRecursive($ra1);
        $this->assertSame(
            [
                'a' => ['aa' => [
                    'aaa' => 1,
                'aab' => [2, 3],
                'aac' => 3,
                ], [
                    'a0a' => 1,
                'a0b' => 2,
                ], [
                    'a0a' => 2,
                'a0c' => 3,
                ]],
                'b' => [1, 'bb' => 3],
                'c',
                'd',
            ],
            $merged1->toArray()
        );
        $this->assertSame(
            [
                'a' => ['aa' => [
                    'aab' => [3, 2],
                'aac' => 3,
                'aaa' => 1,
                ], [
                    'a0a' => 2,
                'a0c' => 3,
                ], [
                    'a0a' => 1,
                'a0b' => 2,
                ]],
                'b' => ['bb' => 3, 1],
                'd',
                'c',
            ],
            $merged2->toArray()
        );
    }

    public function testMerge(): void
    {
        $ra1 = new RA([
            'a' => ['aa' => [
                'aaa' => 1,
            'aab' => 2,
            ], [
                'a0a' => 1,
            'a0b' => 2,
            ]],
            'b' => 1,
            'c',
        ]);
        $ra2 = new RA([
            'a' => ['aa' => [
                'aab' => 3,
            'aac' => 3,
            ], [
                'a0a' => 2,
            'a0c' => 3,
            ]],
            'b' => ['bb' => 3],
            'd',
        ]);
        $merged1 = $ra1->merge($ra2);
        $merged2 = $ra2->merge($ra1);
        $this->assertSame(
            [
                'a' => ['aa' => [
                    'aab' => 3,
                'aac' => 3,
                ], [
                    'a0a' => 2,
                'a0c' => 3,
                ]],
                'b' => ['bb' => 3],
                'c',
                'd',
            ],
            $merged1->toArray()
        );
        $this->assertSame(
            [
                'a' => ['aa' => [
                    'aaa' => 1,
                'aab' => 2,
                ], [
                    'a0a' => 1,
                'a0b' => 2,
                ]],
                'b' => 1,
                'd',
                'c',
            ],
            $merged2->toArray()
        );
    }

    public function testPad(): void
    {
        $ra = new RA(['a', 'b']);
        $padded = $ra->pad(4, 'test');
        $this->assertSame(['a', 'b', 'test', 'test'], $padded->toArray());
    }

    public function testProduct(): void
    {
        $ra = new RA([1, 2, 3, 4, 5]);
        $product1 = $ra->product();
        $this->assertSame(120, $product1);

        $ra = new RA([0.1, 0.2, 0.3, 0.4, 0.5]);
        $product2 = $ra->getProduct();
        $this->assertSame(0.00120, $product2);
    }

    public function testRandom(): void
    {
        $ra = new RA(['a', 'b', 'c', 'd']);
        $randomEntries = $ra->random(2);
        $this->assertSame(2, $randomEntries->length());
        foreach ($randomEntries as $entry) {
            $this->assertContains($entry, ['a', 'b', 'c', 'd']);
        }
        $randomEntry = $ra->getRandomEntry();
        $this->assertContains($randomEntry, ['a', 'b', 'c', 'd']);
        $randomValue = $ra->getRandomValue();
        $this->assertContains($randomValue, ['a', 'b', 'c', 'd']);
        $randomItem = $ra->getRandomItem();
        $this->assertContains($randomItem, ['a', 'b', 'c', 'd']);
    }

    public function testReduce(): void
    {
        $ra = new RA(['a', 'b', 'c', 'd']);
        $reduced = $ra->reduce(function ($carry, $entry) {
            return $carry .= $entry;
        }, '');
        $this->assertSame('abcd', $reduced);
    }

    public function testReplaceRecursive(): void
    {
        $ra1 = new RA([
            'a' => ['aa' => [
                'aaa' => 1,
            'aab' => 2,
            ], [
                'a0a' => 1,
            'a0b' => 2,
            ]],
            'b' => 1,
            'c',
        ]);
        $ra2 = new RA([
            'a' => ['aa' => [
                'aab' => 3,
            'aac' => 3,
            ], [
                'a0a' => 2,
            'a0c' => 3,
            ]],
            'b' => ['bb' => 3],
            'd',
        ]);
        $replaced1 = $ra1->replaceRecursive($ra2);
        $replaced2 = $ra2->replaceRecursive($ra1);
        $this->assertSame(
            [
                'a' => ['aa' => [
                    'aaa' => 1,
                'aab' => 3,
                'aac' => 3,
                ], [
                    'a0a' => 2,
                'a0b' => 2,
                'a0c' => 3,
                ]],
                'b' => ['bb' => 3],
                'd',
            ],
            $replaced1->toArray()
        );
        $this->assertSame(
            [
                'a' => ['aa' => [
                    'aab' => 2,
                'aac' => 3,
                'aaa' => 1,
                ], [
                    'a0a' => 1,
                'a0c' => 3,
                'a0b' => 2,
                ]],
                'b' => 1,
                'c',
            ],
            $replaced2->toArray()
        );
    }

    public function testReplace(): void
    {
        $ra1 = new RA([
            'a' => ['aa' => [
                'aaa' => 1,
            'aab' => 2,
            ], [
                'a0a' => 1,
            'a0b' => 2,
            ]],
            'b' => 1,
            'c',
        ]);
        $ra2 = new RA([
            'a' => ['aa' => [
                'aab' => 3,
            'aac' => 3,
            ], [
                'a0a' => 2,
            'a0c' => 3,
            ]],
            'b' => ['bb' => 3],
            'd',
        ]);
        $replaced1 = $ra1->replace($ra2);
        $replaced2 = $ra2->replace($ra1);
        $this->assertSame(
            [
                'a' => ['aa' => [
                    'aab' => 3,
                'aac' => 3,
                ], [
                    'a0a' => 2,
                'a0c' => 3,
                ]],
                'b' => ['bb' => 3],
                'd',
            ],
            $replaced1->toArray()
        );
        $this->assertSame(
            [
                'a' => ['aa' => [
                    'aaa' => 1,
                'aab' => 2,
                ], [
                    'a0a' => 1,
                'a0b' => 2,
                ]],
                'b' => 1,
                'c',
            ],
            $replaced2->toArray()
        );
    }

    public function testReverse(): void
    {
        $ra = new RA(['a', 'b', 'c', 'd']);
        $reversed = $ra->reverse();
        $this->assertSame(['d', 'c', 'b', 'a'], $reversed->toArray());
    }

    public function testSearch(): void
    {
        $ra = new RA(['a', 'b', 'c', 'd']);
        $b = $ra->search('b');
        $e = $ra->find('e');
        $this->assertSame('b', $b);
        $this->assertNull($e);
    }

    public function testShift(): void
    {
        $ra = new RA(['a', 'b', 'c', 'd']);
        $shifted = $ra->shift();
        $this->assertSame('a', $shifted);
        $this->assertSame(['b', 'c', 'd'], $ra->toArray());
    }

    public function testSlice(): void
    {
        $ra = new RA(['a', 'b', 'c', 'd']);
        $slice1 = $ra->slice(1, 2, false);
        $slice2 = $ra->slice(1, 2, true);
        $this->assertSame(['b', 'c'], $slice1->toArray());
        $this->assertSame([
            1 => 'b',
            2 => 'c',
        ], $slice2->toArray());
    }

    public function testSplice(): void
    {
        $ra = new RA(['a', 'b', 'c', 'd']);
        $splice1 = $ra->splice(1, 2, ['test1', 'test2']);
        $this->assertSame(['a', 'test1', 'test2', 'd'], $ra->toArray());
        $splice2 = $ra->splice(1, 2);
        $this->assertSame(['a', 'd'], $ra->toArray());
        $this->assertSame(['b', 'c'], $splice1->toArray());
        $this->assertSame(['test1', 'test2'], $splice2->toArray());
    }

    public function testSum(): void
    {
        $ra1 = new RA([1, 2, 3, 4, 5]);
        $sum1 = $ra1->sum();
        $this->assertSame(15, $sum1);

        $ra2 = new RA([0.1, 0.2, 0.3, 0.4, 0.5]);
        $sum2 = $ra2->getSum();
        $this->assertSame(1.5, $sum2);
    }

    public function testUdiffAssoc(): void
    {
        $ra1 = new RA(['a', 'b', 'c', 'd']);
        $ra2 = new RA(['A', 'c']);
        $ra3 = new RA(['c', 'B', 'f']);
        $diff = $ra1->udiffAssoc($ra2, $ra3, function ($value1, $value2) {
            return strtolower($value1) === strtolower($value2) ? 0 : 1;
        });
        $this->assertSame([
            2 => 'c',
            3 => 'd',
        ], $diff->toArray());
    }

    public function testUdiffUassoc(): void
    {
        $ra1 = new RA(['a', 'b', 'c', 'd']);
        $ra2 = new RA(['A', 'c']);
        $ra3 = new RA(['c', 'B', 'f']);
        $diff = $ra1->udiffUassoc($ra2, $ra3, function ($value1, $value2) {
            return strtolower($value1) === strtolower($value2) ? 0 : 1;
        }, function ($key1, $key2) {
            return $key1 < 1
                ? $key1 === $key2 ? 0 : 1
                : -1;
        });
        $this->assertSame([
            1 => 'b',
            2 => 'c',
            3 => 'd',
        ], $diff->toArray());
    }

    public function testUdiff(): void
    {
        $ra1 = new RA(['a', 'b', 'c', 'd']);
        $ra2 = new RA(['A', 'c']);
        $ra3 = new RA(['c', 'B', 'f']);
        $diff = $ra1->udiff($ra2, $ra3, function ($value1, $value2) {
            $v1 = strtolower($value1);
            $v2 = strtolower($value2);
            return $v1 === $v2 ? 0 : ($v1 > $v2 ? -1 : 1);
        });
        $this->assertSame([3 => 'd'], $diff->toArray());
    }

    public function testUintersectAssoc(): void
    {
        $ra1 = new RA(['a', 'a', 'c', 'd']);
        $ra2 = new RA(['A', 'b']);
        $ra3 = new RA(['a', 'c', 'f']);
        $intersect = $ra1->uintersectAssoc($ra2, $ra3, function ($value1, $value2) {
            return strtolower((string)$value1) === strtolower((string)$value2) ? 0 : 1;
        });
        $this->assertSame(['a'], $intersect->toArray());
    }

    public function testUintersectUassoc(): void
    {
        $ra1 = new RA(['a', 'b', 'c', 'd']);
        $ra2 = new RA(['A', 'b']);
        $ra3 = new RA(['a', 'B', 'f']);
        $intersect = $ra1->uintersectUassoc($ra2, $ra3, function ($value1, $value2) {
            return strtolower((string)$value1) === strtolower((string)$value2) ? 0 : 1;
        }, function ($key1, $key2) {
            return $key1 > 0
                ? $key1 === $key2 ? 1 : 0
                : 0;
        });
        $this->assertSame(['a'], $intersect->toArray());
    }

    public function testUintersect(): void
    {
        $ra1 = new RA(['a', 'b', 'c', 'd']);
        $ra2 = new RA(['C', 'A']);
        $ra3 = new RA(['a', 'c', 'f']);
        $intersect = $ra1->uintersect($ra2, $ra3, function ($value1, $value2) {
            $v1 = strtolower($value1);
            $v2 = strtolower($value2);
            return $v1 === $v2 ? 0 : ($v1 > $v2 ? -1 : 1);
        });
        $this->assertSame([
            0 => 'a',
            2 => 'c',
        ], $intersect->toArray());
    }

    public function testUnique(): void
    {
        $ra = new RA(['a', 'b', 'c', 'd', 'a', 'c']);
        $unique = $ra->unique();
        $this->assertSame(['a', 'b', 'c', 'd'], $unique->toArray());
    }

    public function testUnshift(): void
    {
        $ra = new RA(['a', 'b']);
        $ra->unshift('c', 'd');
        $this->assertSame(['c', 'd', 'a', 'b'], $ra->toArray());
    }

    public function testValues(): void
    {
        $ra = new RA([
            'a' => 'test1',
            'b' => 'test2',
            'c' => 'test3',
            'd' => 'test4',
        ]);
        $values1 = $ra->values();
        $this->assertSame(['test1', 'test2', 'test3', 'test4'], $values1->toArray());
        $values2 = $ra->getValues();
        $this->assertSame(['test1', 'test2', 'test3', 'test4'], $values2->toArray());
        $values3 = $ra->entries();
        $this->assertSame(['test1', 'test2', 'test3', 'test4'], $values3->toArray());
        $values4 = $ra->getEntries();
        $this->assertSame(['test1', 'test2', 'test3', 'test4'], $values4->toArray());
        $values5 = $ra->items();
        $this->assertSame(['test1', 'test2', 'test3', 'test4'], $values5->toArray());
        $values6 = $ra->getItems();
        $this->assertSame(['test1', 'test2', 'test3', 'test4'], $values6->toArray());
    }

    public function testWalkRecursive(): void
    {
        $ra = new RA(['a', 'b', 'c' => ['test1', 'test2']]);
        $output = [];
        $ra->walkRecursive(function ($entry, $key, $payload) use (&$output) {
            $output[] = $entry . $payload . $key;
        }, '_');
        $this->assertSame(['a_0', 'b_1', 'test1_0', 'test2_1'], $output);
    }

    public function testWalk(): void
    {
        $ra = new RA(['a', 'b', 'c', 'd']);
        $output = [];
        $ra->walk(function ($entry, $key, $payload) use (&$output) {
            $output[] = $entry . $payload . $key;
        }, '_');
        $this->assertSame(['a_0', 'b_1', 'c_2', 'd_3'], $output);
    }

    public function testArsort(): void
    {
        $ra = new RA([
            'a' => 'd_test',
            'b' => 'a_test',
            'c' => 'c_test',
            'd' => 'b_test',
        ]);
        $ra->arsort();
        $this->assertSame([
            'a' => 'd_test',
            'c' => 'c_test',
            'd' => 'b_test',
            'b' => 'a_test',
        ], $ra->toArray());
    }

    public function testAsort(): void
    {
        $ra = new RA([
            'a' => 'd_test',
            'b' => 'a_test',
            'c' => 'c_test',
            'd' => 'b_test',
        ]);
        $ra->asort();
        $this->assertSame([
            'b' => 'a_test',
            'd' => 'b_test',
            'c' => 'c_test',
            'a' => 'd_test',
        ], $ra->toArray());
    }

    public function testEnd(): void
    {
        $ra = new RA(['a', 'b', 'c', 'd']);
        $this->assertSame('a', $ra->current());
        $end = $ra->end();
        $this->assertSame('d', $end);
        $this->assertSame('d', $ra->current());
    }

    public function testContains(): void
    {
        $ra = new RA(['a', 'b', 'c', 'd']);
        $this->assertTrue($ra->contains('c'));
        $this->assertFalse($ra->contains('e'));
        $this->assertTrue($ra->has('c'));
        $this->assertFalse($ra->has('e'));
        $this->assertTrue($ra->hasValue('c'));
        $this->assertFalse($ra->hasValue('e'));
    }

    public function testKrsort(): void
    {
        $ra = new RA([
            'a' => 'd_test',
            'b' => 'a_test',
            'c' => 'c_test',
            'd' => 'b_test',
        ]);
        $ra->krsort();
        $this->assertSame([
            'd' => 'b_test',
            'c' => 'c_test',
            'b' => 'a_test',
            'a' => 'd_test',
        ], $ra->toArray());
    }

    public function testKsort(): void
    {
        $ra = new RA([
            'a' => 'd_test',
            'b' => 'a_test',
            'c' => 'c_test',
            'd' => 'b_test',
        ]);
        $ra->ksort();
        $this->assertSame([
            'a' => 'd_test',
            'b' => 'a_test',
            'c' => 'c_test',
            'd' => 'b_test',
        ], $ra->toArray());
    }

    public function testNatcasesort(): void
    {
        $ra = new RA(['test1', 'test0', 'test12', 'test2', 'Test4']);
        $ra->natcasesort();
        $this->assertSame([
            1 => 'test0',
            0 => 'test1',
            3 => 'test2',
            4 => 'Test4',
            2 => 'test12',
        ], $ra->toArray());
    }

    public function testNatsort(): void
    {
        $ra = new RA(['test1', 'test0', 'test12', 'test2', 'Test4']);
        $ra->natsort();
        $this->assertSame([
            4 => 'Test4',
            1 => 'test0',
            0 => 'test1',
            3 => 'test2',
            2 => 'test12',
        ], $ra->toArray());
    }

    public function testRange(): void
    {
        $ra = new RA();
        $ra->range(1, 5);
        $this->assertSame([1, 2, 3, 4, 5], $ra->toArray());
        $ra->range(1, 5, 2);
        $this->assertSame([1, 3, 5], $ra->toArray());
    }

    public function testRsort(): void
    {
        $ra = new RA([
            'a' => 'd_test',
            'b' => 'a_test',
            'c' => 'c_test',
            'd' => 'b_test',
        ]);
        $ra->rsort();
        $this->assertSame(['d_test', 'c_test', 'b_test', 'a_test'], $ra->toArray());
    }

    public function testShuffle(): void
    {
        $ra = new RA([
            'a' => 'd_test',
            'b' => 'a_test',
            'c' => 'c_test',
            'd' => 'b_test',
        ]);
        $ra->shuffle();
        $this->assertNotSame([
            'a' => 'd_test',
            'b' => 'a_test',
            'c' => 'c_test',
            'd' => 'b_test',
        ], $ra->toArray());
    }

    public function testSort(): void
    {
        $ra = new RA([
            'a' => 'd_test',
            'b' => 'a_test',
            'c' => 'c_test',
            'd' => 'b_test',
        ]);
        $ra->sort();
        $this->assertSame(['a_test', 'b_test', 'c_test', 'd_test'], $ra->toArray());
    }

    public function testUasort(): void
    {
        $ra = new RA([
            'a' => 'd_test',
            'b' => 'a_test',
            'c' => 'c_test',
            'd' => 'b_test',
        ]);
        $ra->uasort(function ($value1, $value2) {
            return $value1 === $value2 ? 0 : ($value1 > $value2 ? -1 : 1);
        });
        $this->assertSame([
            'a' => 'd_test',
            'c' => 'c_test',
            'd' => 'b_test',
            'b' => 'a_test',
        ], $ra->toArray());
    }

    public function testUksort(): void
    {
        $ra = new RA([
            'a' => 'd_test',
            'b' => 'a_test',
            'c' => 'c_test',
            'd' => 'b_test',
        ]);
        $ra->uksort(function ($key1, $key2) {
            return $key1 === $key2 ? 0 : ($key1 > $key2 ? -1 : 1);
        });
        $this->assertSame([
            'd' => 'b_test',
            'c' => 'c_test',
            'b' => 'a_test',
            'a' => 'd_test',
        ], $ra->toArray());
    }

    public function testUsort(): void
    {
        $ra = new RA([
            'a' => 'd_test',
            'b' => 'a_test',
            'c' => 'c_test',
            'd' => 'b_test',
        ]);
        $ra->usort(function ($value1, $value2) {
            return $value1 === $value2 ? 0 : ($value1 > $value2 ? -1 : 1);
        });
        $this->assertSame(['d_test', 'c_test', 'b_test', 'a_test'], $ra->toArray());
    }
}
