<?php
/**
 * LearnDash non-scalar constants
 *
 * @since 4.5.0
 *
 * @package LearnDash
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! defined( 'LEARNDASH_LICENSE_PANEL_SHOW' ) ) {
	/**
	 * Define LearnDash LMS - Show license panel.
	 *
	 * @since 4.3.0.2
	 * @since 4.18.0 -- Now defaults to true.
	 *
	 * @var bool $value {
	 *    Only one of the following values.
	 *    @type bool true  License panel/tab will be visible. Default.
	 *    @type bool false License panel/tab will not be visible.
	 * }
	 */
	define( 'LEARNDASH_LICENSE_PANEL_SHOW', true );
}
