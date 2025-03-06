<?php

/**
 * Definition that disallows all elements.
 * @warning validateChildren() in this class is actually never called, because
 *          empty elements are corrected in LearnDash_Reports_HTMLPurifier_Strategy_MakeWellFormed
 *          before child definitions are parsed in earnest by
 *          LearnDash_Reports_HTMLPurifier_Strategy_FixNesting.
 *
 * @license LGPL-2.1-or-later
 * Modified by stellarwp on 04-November-2024 using {@see https://github.com/BrianHenryIE/strauss}.
 */
class LearnDash_Reports_HTMLPurifier_ChildDef_Empty extends LearnDash_Reports_HTMLPurifier_ChildDef
{
    /**
     * @type bool
     */
    public $allow_empty = true;

    /**
     * @type string
     */
    public $type = 'empty';

    public function __construct()
    {
    }

    /**
     * @param LearnDash_Reports_HTMLPurifier_Node[] $children
     * @param LearnDash_Reports_HTMLPurifier_Config $config
     * @param LearnDash_Reports_HTMLPurifier_Context $context
     * @return array
     */
    public function validateChildren($children, $config, $context)
    {
        return array();
    }
}

// vim: et sw=4 sts=4
