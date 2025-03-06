<?php

/**
 * This is a stub include that automatically configures the include path.
 *
 * @license LGPL-2.1-or-later
 * Modified by stellarwp on 04-November-2024 using {@see https://github.com/BrianHenryIE/strauss}.
 */

set_include_path(dirname(__FILE__) . PATH_SEPARATOR . get_include_path() );
require_once 'LearnDash_Reports_HTMLPurifier/Bootstrap.php';
require_once 'LearnDash_Reports_HTMLPurifier.autoload.php';

// vim: et sw=4 sts=4
