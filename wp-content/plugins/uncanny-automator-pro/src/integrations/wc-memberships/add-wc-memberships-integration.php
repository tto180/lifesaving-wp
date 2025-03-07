<?php

namespace Uncanny_Automator_Pro;

use Uncanny_Automator_Pro\Integrations\Woocommerce\Tokens\Loopable\Universal\User_Active_Memberships;

/**
 * Class Add_Wc_Memberships_Integration
 *
 * @package Uncanny_Automator_Pro
 */
class Add_Wc_Memberships_Integration {

	/**
	 * Integration code
	 *
	 * @var string
	 */
	public static $integration = 'WCMEMBERSHIPS';

	/**
	 * Only load this integration and its triggers and actions if the related plugin is active
	 *
	 * @param $status
	 * @param $plugin
	 *
	 * @return bool
	 */
	public function plugin_active( $status, $plugin ) {
		if ( self::$integration === $plugin ) {
			if ( class_exists( 'WooCommerce' ) && class_exists( 'WC_Memberships_Loader' ) ) {
				$status = true;
			} else {
				$status = false;
			}
		}

		return $status;
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
		$directory[] = dirname( __FILE__ ) . '/triggers';
		$directory[] = dirname( __FILE__ ) . '/tokens';
		$directory[] = dirname( __FILE__ ) . '/conditions';
		$directory[] = dirname( __FILE__ ) . '/loop-filters';

		return $directory;
	}

	/**
	 * Register the integration by pushing it into the global automator object
	 */
	public function add_integration_func() {

		Automator()->register->integration(
			self::$integration,
			array(
				'name'            => 'Woo Memberships',
				'icon_svg'        => \Uncanny_Automator\Utilities::get_integration_icon( 'woocommerce-icon.svg' ),
				'loopable_tokens' => array( 'ACTIVE_MEMBERSHIPS' => User_Active_Memberships::class ),
			)
		);
	}

}
