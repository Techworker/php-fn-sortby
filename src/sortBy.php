<?php

namespace techworker\fn\sortby;

// define method name for partials
const SORT_BY = __NAMESPACE__ . '\sortBy';

/**
 * Returns an callable object that can either be used with u[a]sort directly or
 * extended via its SortByInterface method to add the next level sort order which then
 * returns an callable object that can either be used with u[a]sort directly or
 * extended via its SortByInterface method... and so on.
 *
 * @param \Closure|mixed $comparator
 *        A comparison function (or sth. else) that can be understood by the
 *        decorator.
 * @param int $direction
 *        The direction in which the values should be sorted. Use the PHP
 *        constants SORT_ASC and SORT_DESC.
 * @param callable $decorator
 *        A decorator function that can handle the input of $comparator and
 *        $direction to firstBy and sortByInterface and can transform it to a valid
 *        comparison function (if necessary). If you do not provide a
 *        decorator, the internal one of the fn-thenby package is used.
 *        The usage of this functionality should not be necessary in normal cases.
 *
 * @return ThenByInterface
 */
function sortBy($comparator, int $direction = \SORT_ASC, callable $decorator = null) : ThenByInterface
{
    $decorator = $decorator ?: require(__DIR__ . '/decorator.php');

    // return a new class instance with a public sortByInterface method
    return new class($decorator($comparator, $direction), $decorator)
        implements ThenByInterface
    {
        /**
         * The decorator that will be delegated to the next instance of the
         * firstBy result and used to decorate the sortByInterface camparator.
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
         * @param \Closure $decorator The decorator for sortByInterface calls.
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