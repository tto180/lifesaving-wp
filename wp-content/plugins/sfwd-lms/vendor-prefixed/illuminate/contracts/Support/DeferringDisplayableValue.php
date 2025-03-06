<?php

namespace StellarWP\Learndash\Illuminate\Contracts\Support;

interface DeferringDisplayableValue
{
    /**
     * Resolve the displayable value that the class is deferring.
     *
     * @return \StellarWP\Learndash\Illuminate\Contracts\Support\Htmlable|string
     */
    public function resolveDisplayableValue();
}
