<?php
/**
 * Handles all server side logic for the ld-propanel-reporting Gutenberg Block.
 *
 * @since 4.17.0
 *
 * @package LearnDash
 */

defined( 'ABSPATH' ) || exit;

if ( ( class_exists( 'LearnDash_ProPanel_Gutenberg_Block' ) ) && ( ! class_exists( 'LearnDash_ProPanel_Gutenberg_Block_Reporting' ) ) ) {
	/**
	 * Class for handling LearnDash Login Block
	 */
	class LearnDash_ProPanel_Gutenberg_Block_Reporting extends LearnDash_ProPanel_Gutenberg_Block {
		/**
		 * Object constructor
		 */
		public function __construct() {
			$this->shortcode_slug   = 'ld_propanel';
			$this->shortcode_widget = 'reporting';
			$this->block_slug       = 'ld-propanel-reporting';
			$this->block_attributes = array(
				'preview_show'   => array(
					'type' => 'boolean',
				),
				'filter_groups'  => array(
					'type' => 'int',
				),
				'filter_courses' => array(
					'type' => 'int',
				),
				'filter_users'   => array(
					'type' => 'int',
				),
				'filter_status'  => array(
					'type' => 'string',
				),
				'per_page'       => array(
					'type' => 'int',
				),
			);
			$this->self_closing     = true;

			$this->init();
		}

		/** This function is documented in includes/gutenberg/lib/class-learndash-propanel-gutenberg-block.php */
		protected function process_block_attributes( $block_attributes = array() ) {
			if ( isset( $block_attributes['preview_show'] ) ) {
				unset( $block_attributes['preview_show'] );
			}

			return $block_attributes;
		}

		// End of functions.
	}
}
new LearnDash_ProPanel_Gutenberg_Block_Reporting();
