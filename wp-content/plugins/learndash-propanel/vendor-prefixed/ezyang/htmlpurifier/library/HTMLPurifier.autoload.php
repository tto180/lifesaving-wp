<?php

/**
 * @file
 * Convenience file that registers autoload handler for HTML Purifier.
 * It also does some sanity checks.
 *
 * @license LGPL-2.1-or-later
 * Modified by stellarwp on 04-November-2024 using {@see https://github.com/BrianHenryIE/strauss}.
 */

if (function_exists('spl_autoload_register') && function_exists('spl_autoload_unregister')) {
    // We need unregister for our pre-registering functionality
    LearnDash_Reports_HTMLPurifier_Bootstrap::registerAutoload();
    if (function_exists('__autoload')) {
        // Be polite and ensure that userland autoload gets retained
        spl_autoload_register('__autoload');
    }
} elseif (!function_exists('__autoload')) {
    require dirname(__FILE__) . '/LearnDash_Reports_HTMLPurifier.autoload-legacy.php';
}

// phpcs:ignore PHPCompatibility.IniDirectives.RemovedIniDirectives.zend_ze1_compatibility_modeRemoved
if (ini_get('zend.ze1_compatibility_mode')) {
    trigger_error("HTML Purifier is not compatible with zend.ze1_compatibility_mode; please turn it off", E_USER_ERROR);
}

// vim: et sw=4 sts=4
