<?php

namespace techworker\fn\sortby;

// this is part of another side project, please ignore it for now
const FN_SORT_BY = __NAMESPACE__ . '\sortBy';

/**
 * Prepares a sort function to be used in combination with [usort](https://php.net/usort) or
 * [uasort](https://php.net/uasort) to sort an array of any shape in a functional way.
 *
 * @param callable|int|string $comparator
 *        This can either be a string or a callable function that returns the value to sort by.
 * @param int $direction
 *        The direction to sort by. This can be one of the two predefined `SORT_*` constants in PHP:
 *        [`\SORT_ASC`](http://php.net/manual/en/array.constants.php#constant.sort-asc) AND
 *        [`\SORT_DESC`](http://php.net/manual/de/array.constants.php#constant.sort-desc).
 * @param callable $decorator
 *        A decorator function that can handle the input of $comparator and $direction to sortBy
 *        and ThenByInterface and can transform it to a valid comparison function (if necessary).
 *        If you do not provide a decorator, the internal one of the php-fn-sortby package is used.
 *        The usage of this functionality should not be necessary in normal cases.
 *
 * @return ThenByInterface
 */
function sortBy($comparator, int $direction = \SORT_ASC, callable $decorator = null) : ThenByInterface
{
    // use the given decorator or the builtin one
    $decorator = $decorator ?: require(__DIR__ . '/Decorator.php');

    // return a new class instance with a public ThenByInterface method
    return new class($decorator($comparator, $direction), $decorator)
        implements ThenByInterface
    {
        /**
         * The decorator that will be delegated to the next instance of the
         * sortBy result and used to decorate the ThenByInterface comparator.
         *
         * @var \Closure
         */
        protected $decorator;

        /**
         * The comparator function.
         *
         * @var \Closure
         */
        protected $comparator;

        /**
         * Constructor.
         *
         * @param \Closure $comparator The comparator function.
         * @param \Closure $decorator The decorator for ThenByInterface calls.
         */
        public function __construct(\Closure $comparator, \Closure $decorator) {
            $this->comparator = $comparator;
            $this->decorator = $decorator;
        }

        /**
         * Calls the current comparator \Closure with the given values to
         * compare.
         *
         * @param array ...$args The values to compare.
         * @return int
         */
        public function __invoke(...$args) : int {
            return $this->comparator->__invoke($args[0], $args[1]);
        }

        /**
         * Adds another comparator to the previous comparator.
         *
         * @param \Closure|string $comparator
         * @param int $direction
         * @return ThenByInterface
         */
        public function thenBy($comparator, int $direction = SORT_ASC) : ThenByInterface
        {
            // decorate the given comparator
            $comparator = $this->decorator->__invoke($comparator, $direction);

            // return new instance of the anonymous class
            return new self(function ($v1, $v2) use ($comparator) {
                // check if the current comparator returns 0 (false), if so
                // the child comparator is called.
                return $this->__invoke($v1, $v2) ?: $comparator($v1, $v2);
                // $this($v1, $v2) PHPStorm highlights error
            }, $this->decorator);
        }
    };
};