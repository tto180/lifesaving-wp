<?php

namespace uncanny_learndash_groups;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Class ProcessManualGroup
 * @package uncanny_learndash_groups
 */
class ProcessGroupDeletion {
	/**
	 * ProcessManualGroup constructor.
	 */
	public function __construct() {
		//remove group related data from custom tables
		add_action( 'after_delete_post', array( __CLASS__, 'remove_related_groups_data' ) );
	}

	/**
	 * @param $post_id
	 *
	 * @return bool|int|\mysqli_result|resource|null
	 */
	public static function remove_related_groups_data( $post_id ) {
		if ( ! $post_id ) {
			return false;
		}

		global $wpdb;

		$group_detail_id = ulgm()->group_management->seat->get_code_group_id( $post_id );
		if ( ! is_numeric( $group_detail_id ) ) {
			return false;
		}
		//if ( $group_detail_id ) {
		$r1 = $wpdb->query( $wpdb->prepare( "DELETE FROM $wpdb->prefix" . ulgm()->db->tbl_group_details . ' WHERE ID=%d', $group_detail_id ) ); //phpcs:ignore
		if ( ! is_numeric( $r1 ) ) {
			return false;
		}

		$r2 = $wpdb->query( $wpdb->prepare( "DELETE FROM $wpdb->prefix" . ulgm()->db->tbl_group_codes . ' WHERE group_id=%d', $group_detail_id ) ); //phpcs:ignore

		return is_numeric( $r2 );
		//}
	}

}
