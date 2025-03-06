<?php

/**
 * @file
 * Defines a function wrapper for HTML Purifier for quick use.
 * @note ''LearnDash_Reports_HTMLPurifier()'' is NOT the same as ''new LearnDash_Reports_HTMLPurifier()''
 *
 * @license LGPL-2.1-or-later
 * Modified by stellarwp on 04-November-2024 using {@see https://github.com/BrianHenryIE/strauss}.
 */

/**
 * Purify HTML.
 * @param string $html String HTML to purify
 * @param mixed $config Configuration to use, can be any value accepted by
 *        LearnDash_Reports_HTMLPurifier_Config::create()
 * @return string
 */
function LearnDash_Reports_HTMLPurifier($html, $config = null)
{
    static $purifier = false;
    if (!$purifier) {
        $purifier = new LearnDash_Reports_HTMLPurifier();
    }
    return $purifier->purify($html, $config);
}

// vim: et sw=4 sts=4
