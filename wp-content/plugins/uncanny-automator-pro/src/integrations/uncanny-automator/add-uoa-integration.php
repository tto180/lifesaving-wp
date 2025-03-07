<?php

namespace Uncanny_Automator_Pro;

/**
 * Class Add_Uoa_Integration
 *
 * @package Uncanny_Automator_Pro
 */
class Add_Uoa_Integration {

	/**
	 * Integration code
	 *
	 * @var string
	 */
	public static $integration = 'UOA';

	/**
	 * Add_Integration constructor.
	 */
	public function __construct() {
	}

	/**
	 * Only load this integration and its triggers and actions if the related plugin is active
	 *
	 * @param $status
	 * @param $code
	 *
	 * @return bool
	 */
	public function plugin_active( $status, $code ) {
		return true;
	}


	/**
	 * Set the directories that the auto loader will run in
	 *
	 * @param $directory
	 *
	 * @return array
	 */
	public function add_integration_directory_func( $directory ) {

		$directory[] = dirname( __FILE__ ) . '/helpers';
		$directory[] = dirname( __FILE__ ) . '/actions';
		//$directory[] = dirname( __FILE__ ) . '/triggers';
		$directory[] = dirname( __FILE__ ) . '/tokens';
		$directory[] = dirname( __FILE__ ) . '/closures';
		$directory[] = dirname( __FILE__ ) . '/conditions';
		$directory[] = dirname( __FILE__ ) . '/loop-filters';

		return $directory;
	}

	/**
	 * Register the integration by pushing it into the global automator object
	 */
	public function add_integration_func() {

		Automator()->register->integration(
			'UOA',
			array(
				'name'     => 'Automator Core',
				'icon_svg' => \Uncanny_Automator_Pro\Utilities::get_integration_icon( 'automator-core-icon.svg' ),
			)
		);
	}
}
