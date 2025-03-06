<?php

namespace StellarWP\Learndash\Illuminate\Contracts\Database\Eloquent;

interface Castable
{
    /**
     * Get the name of the caster class to use when casting from / to this cast target.
     *
     * @param  array  $arguments
     * @return string
     * @return string|\StellarWP\Learndash\Illuminate\Contracts\Database\Eloquent\CastsAttributes|\StellarWP\Learndash\Illuminate\Contracts\Database\Eloquent\CastsInboundAttributes
     */
    public static function castUsing(array $arguments);
}
