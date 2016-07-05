<?php

namespace techworker\fn\sortBy;

/*
 * A decorator function to transform a comparator of any type to a comparator usable in
 * combination with usort/uasort.
 *
 * @param int|string|callable $comparator The comparator value.
 * @param int $direction The sort direction.
 */
return /* PHPStorm */$a = function ($comparator, $direction = \SORT_ASC) : callable {
    // check if the given value is not a function
    if (!is_callable($comparator)) {
        // make unary function
        $comparator = function ($v) use ($comparator) {
            if (is_array($v) && isset($v[$comparator])) {
                return $v[$comparator];
            }
            if (is_object($v) && isset($v->{$comparator})) {
                return $v->{$comparator};
            }

            assert('false', 'The property or key you gave to techworker\fn\\sortBy|thenBy does not exist.');
            return 0;
        };
    }

    //  now check how many parameters the function has, if only 1 we will wrap it into a real
    // comparer function
    if ((new \ReflectionFunction($comparator))->getNumberOfParameters() === 1) {
        $comparator = function ($v1, $v2) use ($comparator) {
            return $comparator($v1) <=> $comparator($v2);
        };
    }

    // check if the sort mode is descending
    if ($direction === \SORT_DESC) {
        // make another decorator and return the inverted cmp value
        return function ($v1, $v2) use ($comparator) {
            return -$comparator($v1, $v2);
        };
    }

    return $comparator;
};
