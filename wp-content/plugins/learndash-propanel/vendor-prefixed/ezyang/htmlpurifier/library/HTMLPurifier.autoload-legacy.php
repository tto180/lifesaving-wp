<?php

/**
 * @file
 * Legacy autoloader for systems lacking spl_autoload_register
 *
 *
 * @license LGPL-2.1-or-later
 * Modified by stellarwp on 04-November-2024 using {@see https://github.com/BrianHenryIE/strauss}.
 */

spl_autoload_register(function($class)
{
     return LearnDash_Reports_HTMLPurifier_Bootstrap::autoload($class);
});

// vim: et sw=4 sts=4
