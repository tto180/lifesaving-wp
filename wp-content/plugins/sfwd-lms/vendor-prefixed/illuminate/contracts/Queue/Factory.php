<?php

namespace StellarWP\Learndash\Illuminate\Contracts\Queue;

interface Factory
{
    /**
     * Resolve a queue connection instance.
     *
     * @param  string|null  $name
     * @return \StellarWP\Learndash\Illuminate\Contracts\Queue\Queue
     */
    public function connection($name = null);
}
