<?php

/**
 * Writes default type for all objects. Currently only supports flash.
 *
 * @license LGPL-2.1-or-later
 * Modified by stellarwp on 04-November-2024 using {@see https://github.com/BrianHenryIE/strauss}.
 */
class LearnDash_Reports_HTMLPurifier_AttrTransform_SafeObject extends LearnDash_Reports_HTMLPurifier_AttrTransform
{
    /**
     * @type string
     */
    public $name = "SafeObject";

    /**
     * @param array $attr
     * @param LearnDash_Reports_HTMLPurifier_Config $config
     * @param LearnDash_Reports_HTMLPurifier_Context $context
     * @return array
     */
    public function transform($attr, $config, $context)
    {
        if (!isset($attr['type'])) {
            $attr['type'] = 'application/x-shockwave-flash';
        }
        return $attr;
    }
}

// vim: et sw=4 sts=4
