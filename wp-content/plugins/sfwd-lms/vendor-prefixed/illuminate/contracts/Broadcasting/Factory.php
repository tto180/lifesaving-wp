<?php

namespace StellarWP\Learndash\Illuminate\Contracts\Broadcasting;

interface Factory
{
    /**
     * Get a broadcaster implementation by name.
     *
     * @param  string|null  $name
     * @return \StellarWP\Learndash\Illuminate\Contracts\Broadcasting\Broadcaster
     */
    public function connection($name = null);
}
