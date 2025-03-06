<?php

namespace StellarWP\Learndash\Illuminate\Contracts\Mail;

interface Factory
{
    /**
     * Get a mailer instance by name.
     *
     * @param  string|null  $name
     * @return \StellarWP\Learndash\Illuminate\Contracts\Mail\Mailer
     */
    public function mailer($name = null);
}
