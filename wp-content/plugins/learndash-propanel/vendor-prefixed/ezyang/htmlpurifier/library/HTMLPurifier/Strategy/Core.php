<?php

/**
 * Core strategy composed of the big four strategies.
 *
 * @license LGPL-2.1-or-later
 * Modified by stellarwp on 04-November-2024 using {@see https://github.com/BrianHenryIE/strauss}.
 */
class LearnDash_Reports_HTMLPurifier_Strategy_Core extends LearnDash_Reports_HTMLPurifier_Strategy_Composite
{
    public function __construct()
    {
        $this->strategies[] = new LearnDash_Reports_HTMLPurifier_Strategy_RemoveForeignElements();
        $this->strategies[] = new LearnDash_Reports_HTMLPurifier_Strategy_MakeWellFormed();
        $this->strategies[] = new LearnDash_Reports_HTMLPurifier_Strategy_FixNesting();
        $this->strategies[] = new LearnDash_Reports_HTMLPurifier_Strategy_ValidateAttributes();
    }
}

// vim: et sw=4 sts=4
