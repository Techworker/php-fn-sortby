<?php

namespace techworker\fn\sortBy;

/**
 * Interface for returned instances from firstBy and sortByInterface. Just to
 * have proper auto-completion in the IDE as I'm not sure how to document
 * anonymous classes as a return type properly.
 */
interface ThenByInterface
{
    /**
     * @param $comparator
     * @param int $direction
     *
     * @return ThenByInterface|callable
     */
    public function thenBy($comparator, int $direction = SORT_ASC) : ThenByInterface;
}
