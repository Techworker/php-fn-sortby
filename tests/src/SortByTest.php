<?php

namespace techworker\fn\sortby\tests;

use PHPUnit\Framework\TestCase;
use function \techworker\fn\sortBy\sortBy;
use techworker\fn\sortby\ThenByInterface;

class SortByTest extends TestCase
{
    public function namesProvider()
    {
        $names = [
            ['name' => 'zoey'],
            ['name' => 'ben'],
            ['name' => 'raffa']
        ];

        $namesObj = [];
        foreach($names as $name) {
            $obj = new \stdClass();
            $obj->name = $name['name'];
            $namesObj[] = $obj;
        }

        return [[$names], [$namesObj]];
    }

    public function sortedNamesProvider()
    {
        return [
            ['name' => 'ben'],
            ['name' => 'raffa'],
            ['name' => 'zoey']
        ];
    }

    public function testReturnsInterface()
    {
        $sorter = sortBy('name');
        static::assertInstanceOf(ThenByInterface::class, $sorter);
        $sorter->thenBy('foo');
        static::assertInstanceOf(ThenByInterface::class, $sorter);
    }
    /**
     * @dataProvider namesProvider
     */
    public function testArrayKeyOrPropertySort($names)
    {
        $sorted = $this->sortedNamesProvider();
        $sorter = sortBy('name');
        usort($names, $sorter);
        foreach($names as $k => $name)
        {
            static::assertEquals(is_object($name) ? $name->name : $name['name'], $sorted[$k]['name']);
        }
    }
}