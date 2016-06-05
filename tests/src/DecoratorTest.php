<?php

namespace techworker\fn\sortby\tests;

use phpunit\framework\TestCase;
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
        $this->decorator = require __DIR__ . '/../../src/decorator.php';
    }

    protected function callDecorator(...$params)
    {
        return call_user_func_array($this->decorator, $params);
    }

    public function testArrayKeyNameProperty()
    {
        $callback = $this->callDecorator('name');
        $a = ['name' => 'A'];
        $z = ['name' => 'Z'];
        static::assertEquals(0, $callback($a, $a));
        static::assertEquals(-1, $callback($a, $z));
        static::assertEquals(1, $callback($z, $a));
    }

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
     * @expectedException \PHPUnit_Framework_Error_Warning
     */
    public function testUnknownArrayKeyOrObjectProperty()
    {
        ini_set('assert.active', 1);
        $callback = $this->callDecorator('foobar');

        $test = new \stdClass();
        $test->abc = 'def';
        static::assertEquals(0, $callback($test, $test));
    }
}