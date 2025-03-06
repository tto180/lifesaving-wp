<?php

namespace StellarWP\Learndash\Illuminate\Contracts\Support;

interface MessageProvider
{
    /**
     * Get the messages for the instance.
     *
     * @return \StellarWP\Learndash\Illuminate\Contracts\Support\MessageBag
     */
    public function getMessageBag();
}
