<?php

namespace techworker\fn\sortby\tests;

use PHPUnit\Framework\TestCase;
use function \techworker\fn\sortBy\sortBy;

class DecoratorTest extends TestCase
{
    /**
     * @var \callable
     */
    protected $decorator;

    /**
     * DecoratorTest constructor.
     */
    public function __construct(...$whatever)
    {
        parent::__construct(...$whatever);
        $this->decorator = require __DIR__ . '/../../src/Decorator.php';
    }

    /**
     * @param array ...$params
     * @return mixed Simple helper to call a decorator.
     */
    protected function callDecorator(...$params)
    {
        return call_user_func_array($this->decorator, $params);
    }

    /**
     * Tests if a decorator can access array keys.
     */
    public function testArrayKeyNameProperty()
    {
        $callback = $this->callDecorator('name');
        $a = ['name' => 'A'];
        $z = ['name' => 'Z'];
        static::assertEquals(0, $callback($a, $a));
        static::assertEquals(-1, $callback($a, $z));
        static::assertEquals(1, $callback($z, $a));
    }

    /**
     * Tests of a decorator can access object properties.
     */
    public function testObjectKeyNameProperty()
    {
        $callback = $this->callDecorator('name');

        $a = new \stdClass();
        $a->name = 'A';
        $z = new \stdClass();
        $z->name = 'Z';

        static::assertEquals(0, $callback($a, $a));
        static::assertEquals(-1, $callback($a, $z));
        static::assertEquals(1, $callback($z, $a));
    }

    /**
     * Tests if the function returns 0 if its not comparable.
     */
    public function testUnknownArrayKeyOrObjectProperty()
    {
        ini_set('assert.exception', 1);
        $callback = $this->callDecorator('foobar');

        $test = new \stdClass();
        $test->abc = 'def';
        static::assertEquals(0, $callback($test, $test));
    }

    /**
     * Tests if the SORT_DESC parameter works
     */
    public function testSortDesc()
    {
        $callback = $this->callDecorator('foobar', \SORT_DESC);

        $v1 = new \stdClass();
        $v1->foobar = 1;

        $v2 = new \stdClass();
        $v2->foobar = 2;
        static::assertEquals(1, $callback($v1, $v2));
    }
}