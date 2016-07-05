<?php

namespace techworker\fn\sortby;

/**
 * Interface for returned instances from firstBy and sortByInterface. Just to
 * have proper auto-completion in the IDE as I'm not sure how to document
 * anonymous classes as a return type properly.
 */
interface ThenByInterface
{
    public function thenBy($comparator, int $direction = SORT_ASC) : ThenByInterface;
}
