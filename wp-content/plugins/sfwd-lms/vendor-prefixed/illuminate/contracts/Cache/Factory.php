<?php

namespace StellarWP\Learndash\Illuminate\Contracts\Cache;

interface Factory
{
    /**
     * Get a cache store instance by name.
     *
     * @param  string|null  $name
     * @return \StellarWP\Learndash\Illuminate\Contracts\Cache\Repository
     */
    public function store($name = null);
}
