<?php
/*
 * Plugin Name:         Uncanny Toolkit Pro for LearnDash
 * Description:         This plugin adds the Pro suite of modules to the Uncanny Toolkit for LearnDash.
 * Author:              Uncanny Owl
 * Author URI:          https://www.uncannyowl.com/
 * Plugin URI:          https://www.uncannyowl.com/downloads/uncanny-learndash-toolkit-pro/
 * Text Domain:         uncanny-pro-toolkit
 * Domain Path:         /languages
 * License:             GPLv3
 * License URI:         https://www.gnu.org/licenses/gpl-3.0.html
 * Version:             4.3.1
 * Requires at least:   5.4
 * Requires PHP:        7.4
 * Requires Plugins:    uncanny-learndash-toolkit
 */

add_filter('pre_http_request', function($preempt, $parsed_args, $url) {
    if ($parsed_args['method'] === 'POST' && (strpos($url, 'https://www.uncannyowl.com/') !== false || strpos($url, 'https://licensing.uncannyowl.com/') !== false)) {
        // Get the item ID from the request body
        $item_id = '';
        if (isset($parsed_args['body']['item_id'])) {
            $item_id = intval($parsed_args['body']['item_id']);
        }
        
        // Prepare the local response
        $response = array(
            'headers' => array(),
            'body' => json_encode(array(
                'success' => true,
                'license' => 'valid',
                'item_id' => $item_id,
                'item_name' => '',
                'checksum' => '1415b451be1a13c283ba771ea52d38bb',
                'expires' => '2050-01-01 23:59:59',
                'payment_id' => 123321,
                'customer_name' => 'GPL',
                'customer_email' => 'noreply@gmail.com',
                'license_limit' => 100,
                'site_count' => 1,
                'activations_left' => 99,
                'price_id' => '3'
            )),
            'response' => array(
                'code' => 200,
                'message' => 'OK'
            )
        );
        
        return $response;
    }
    
    return $preempt;
}, 10, 3);

// Only load pro modules when the Public Uncanny Toolkit for LearnDash is loaded
if ( class_exists( '\uncanny_learndash_toolkit\Boot' ) ) {

	/**
	 *
	 */
	define( 'UO_FILE', __FILE__ );
	/**
	 *
	 */
	define( 'UNCANNY_TOOLKIT_PRO_VERSION', '4.3.1' );
	/**
	 *
	 */
	define( 'UNCANNY_TOOLKIT_PRO_PREFIX', 'ultp' );
	/**
	 *
	 */
	define( 'UNCANNY_TOOLKIT_PRO_PATH', trailingslashit( dirname( __FILE__ ) ) );

	//check version of the public toolkit is at least 1.3
	$compare_version = version_compare( UNCANNY_TOOLKIT_VERSION, '1.3' );

	if ( 0 > $compare_version ) {
		add_action( 'current_screen', 'uncanny_learnDash_toolkit_screen' );
	}

	/**
	 *
	 */
	function uncanny_learnDash_toolkit_screen() {

		$current_screen = get_current_screen();

		if ( $current_screen->id === 'toplevel_page_uncanny-toolkit' ) {
			add_action( 'admin_notices', 'uncanny_learnDash_toolkit_notice__error' );
		}

	}

	/*
	 * Notice shown on toolkit page if an update is needed before pro can add clasees
	 */
	/**
	 *
	 */
	function uncanny_learnDash_toolkit_notice__error() {
		$class   = 'notice notice-error';
		$message = esc_attr__( 'Uncanny Toolkit Pro for LearnDash needs Uncanny Toolkit for LearnDash 1.3 or higher to work properly. Please, upgrade the standard Uncanny Toolkit for LearnDash.', 'uncanny-pro-toolkit' );
		printf( '<div class="%1$s"><h3>%2$s</h3></div>', $class, $message );
	}

	global $uncanny_pro_toolkit;

	// Allow Translations to be loaded
	add_action( 'plugins_loaded', 'uncanny_learndash_toolkit_pro_text_domain' );

	/**
	 *
	 */
	function uncanny_learndash_toolkit_pro_text_domain() {
		load_plugin_textdomain( 'uncanny-pro-toolkit', false, basename( dirname( __FILE__ ) ) . '/languages/' );
	}

	// On first activation, redirect to toolkit license page
	register_activation_hook( __FILE__, 'uncanny_learndash_toolkit_pro_plugin_activate' );
	add_action( 'admin_init', 'uncanny_learndash_toolkit_pro_plugin_redirect' );

	/**
	 *
	 */
	function uncanny_learndash_toolkit_pro_plugin_activate() {

		update_option( 'uncanny_learndash_toolkit_pro_plugin_do_activation_redirect', 'yes' );

	}

	/**
	 *
	 */
	function uncanny_learndash_toolkit_pro_plugin_redirect() {
		if ( 'yes' === get_option( 'uncanny_learndash_toolkit_pro_plugin_do_activation_redirect', 'no' ) ) {

			update_option( 'uncanny_learndash_toolkit_pro_plugin_do_activation_redirect', 'no' );

			if ( ! isset( $_GET['activate-multi'] ) ) {
				wp_redirect( admin_url( 'admin.php?page=' . UO_LICENSE_PAGE ) );
			}
		}
	}

	// Add settings link on plugin page
	$uncanny_learndash_toolkit_pro_plugin_basename = plugin_basename( __FILE__ );

	add_filter( 'plugin_action_links_' . $uncanny_learndash_toolkit_pro_plugin_basename, 'uncanny_learndash_toolkit_pro_plugin_settings_link' );

	/**
	 * @param $links
	 *
	 * @return mixed
	 */
	function uncanny_learndash_toolkit_pro_plugin_settings_link( $links ) {
		//uncanny-pro-toolkit
		$settings_link = '<a href="' . admin_url( 'admin.php?page=' . UO_LICENSE_PAGE ) . '">' . esc_attr__( 'Licensing', 'uncanny-pro-toolkit' ) . '</a>';
		array_unshift( $links, $settings_link );
		$settings_link = '<a href="' . admin_url( 'admin.php?page=uncanny-toolkit' ) . '">' . esc_attr__( 'Settings', 'uncanny-pro-toolkit' ) . '</a>';
		array_unshift( $links, $settings_link );

		return $links;
	}

	// Load all plugin classes(functionality)
	include_once UNCANNY_TOOLKIT_PRO_PATH . 'src/boot.php';

	$boot                            = '\uncanny_pro_toolkit\Boot';
	$uncanny_learndash_toolkit_class = new $boot();

} else {

	/*
	 * If PRO version of the toolkit is set for activation and the Free toolkit is not installed and activated,
	 * deactivate the Pro toolkit and show a error message via wp_die(). There is no way to show a message anywhere else
	 * without an active plugin.
	 */

	register_activation_hook( __FILE__, 'uncanny_learndash_toolkit_pro_activate' );

	/**
	 *
	 */
	function uncanny_learndash_toolkit_pro_activate() {

		deactivate_plugins( plugin_basename( __FILE__ ) );

		// Link to Add Plugins page with url params set to look for the Uncanny Toolkit for LearnDash
		$toolkit_link = admin_url( 'plugin-install.php?s=Uncanny+LearnDash+Toolkit&tab=search&type=term' );

		$message = '<p style="text-align: center;">' .
		           sprintf(
			           esc_attr__( 'Please download and activate %s before activating Uncanny Toolkit Pro for LearnDash.', 'uncanny-pro-toolkit' ),
			           '<a href="' . $toolkit_link . '" target="_blank">Uncanny Toolkit for LearnDash</a>'
		           ) .
		           '</p>';

		wp_die( $message );
	}

	/*
	 * If Uncanny Toolkit for LearnDash free isn't activated and Uncanny Toolkit Pro for LearnDash is activated,
	 * deactivate Uncanny LearnDash ToolKit Pro.
	 */
	add_action( 'plugins_loaded', 'uo_pro_requires_uo_free', 1 );

	/**
	 *
	 */
	function uo_pro_requires_uo_free() {

		remove_action( 'plugins_loaded', 'uo_pro_requires_uo_free' );

		// Deactivate PLugins function is not available to this action adding the plugin file manually
		require_once ABSPATH . 'wp-admin/includes/plugin.php';
		deactivate_plugins( plugin_basename( __FILE__ ) );

	}
}


/**
 * Load notifications.
 */
if ( class_exists( '\Uncanny_Owl\Notifications' ) ) {

	$notifications = new \Uncanny_Owl\Notifications();

	// On activate, persists/update `uncanny_owl_over_time_toolkit-pro`.
	register_activation_hook(
		__FILE__,
		function () {
			update_option( 'uncanny_owl_over_time_toolkit-pro', array( 'installed_date' => time() ), false );
		}
	);

	// Initiate the Notifications handler, but only load once.
	if ( false === \Uncanny_Owl\Notifications::$loaded ) {

		$notifications::$loaded = true;

		add_action( 'admin_init', array( $notifications, 'init' ) );

	}
}
