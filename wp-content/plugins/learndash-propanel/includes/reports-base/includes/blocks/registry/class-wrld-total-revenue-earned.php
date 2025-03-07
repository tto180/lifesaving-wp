<?php
/**
 * Total revenue earned
 *
 * @package LearnDash\Reports
 */

namespace WisdmReportsLearndashBlockRegistry;

defined( 'ABSPATH' ) || exit;

require_once 'class-wrld-register-block.php';
if ( ! class_exists( '\WisdmReportsLearndashBlockRegistry\WRLD_Total_Revenue_Earned' ) ) {
	/**
	 * This class contains the Functionality required to register the Total Revenue Earned Block
	 */
	class WRLD_Total_Revenue_Earned extends WRLD_Register_Block {
		/**
		 * Constructor.
		 *
		 * @param string $block_name           Set block name during construct.
		 * @param string $block_title          To be displayed in the WP-Admin.
		 * @param string $description          Description of the block.
		 * @param string $server_side_callback Function name, the child class must implement the method specified as this argument.
		 * @param int    $api_version           Block API Version , default 2.
		 */
		public function __construct( $block_name = 'total-revenue-earned', $block_title = 'Total Revenue Earned', $description = 'Displays the total revenue earned during the selected time & its comparison with the previous duration', $server_side_callback = false, $api_version = 2 ) {
			$this->block_name  = $block_name ? $block_name : $this->block_name;
			$this->api_version = $api_version;
			$this->description = $description;
			$this->block_title = $block_title;
			$this->wrld_register_block_assets();
			$this->wrld_register_block_type();
			$this->server_side_callback = 'server_side_render_function';
		}


		/**
		 * The function can be used to render the block content on the server side.
		 */
		public function server_side_render_function() {
			return 'Html if required';
		}
	}
}
