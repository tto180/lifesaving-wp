<?php
/**
 * Other plugins page class file.
 *
 * @deprecated 1.8.2 This file is no longer in use.
 */

namespace WRLDAdmin;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'PluginsPage' ) ) {
	/**
	 * Class for showing tabs of WRLD.
	 */
	class PluginsPage {
		public function __construct() {
			if ( is_rtl() ) {
				// cspell:disable-next-line .
				wp_enqueue_style( 'wrld_admin_dashboard_contentainer_style', WRLD_REPORTS_SITE_URL . '/includes/admin/dashboard/assets/css/content-page.rtl.css', array(), LDRP_PLUGIN_VERSION );
			} else {
				// cspell:disable-next-line .
				wp_enqueue_style( 'wrld_admin_dashboard_contentainer_style', WRLD_REPORTS_SITE_URL . '/includes/admin/dashboard/assets/css/content-page.css', array(), LDRP_PLUGIN_VERSION );
			}
		}

		public static function render() {
			?>
			<div class='wrld-dashboard-page-container'>
				<?php
				self::content_main();
				?>
			</div>
			<?php
		}

		public static function content_main() {
			$plugin_list = array(
				'group_registration' => array(
					'link'    => '',
					'image'   => 'group-registration.png',
					'name'    => 'Group Registration',
					'excerpt' => __( 'Group Registration enables your group leaders to purchase a course and enroll students by adding them as group members. It also has useful features like Auto Group Creation and CSV upload of students.', 'learndash-reports-pro' ),
				),
				'instructor_role'    => array(
					'link'    => '',
					'image'   => 'instructor-role.png',
					'name'    => 'Instructor Role',
					'excerpt' => __( 'Add Instructors to your LearnDash website, with capabilities to create courses content and track student progress. You can create multiple instructors roles and set commissions for paid courses that they create.', 'learndash-reports-pro' ),
				),
				'content_cloner'     => array(
					'link'    => '',
					'image'   => 'content-cloner.png',
					'name'    => 'Content Cloner',
					'excerpt' => __( 'Create similar courses with just a click of a button! The entire course, lessons and topics can duplicate and add to your LearnDash LMS, thus saving your time. The plugin also allows you to duplicate groups and bulk edit titles.', 'learndash-reports-pro' ),
				),
				'elumine'            => array(
					'link'    => '',
					'image'   => 'elumine.png',
					'name'    => 'eLumine',
					// cspell:disable-next-line .
					'excerpt' => __( 'eLumine is the ultimate eLearning theme with multiple customized layouts, superfun gamification, a wide range of compatibility and practical functionalities.', 'learndash-reports-pro' ),
				),
			);
			?>
				<div class='wrld-dashboard-page-content'>
					<div class="other-plugins">
						<span class='wrld-dashboard-text license plugins'> <?php esc_html_e( 'WisdmLabs Plugins', 'learndash-reports-pro' ); ?> </span>
					</div>
					<div class ='wrld-plugins-page-grid'>
						<?php
						foreach ( $plugin_list as $key => $plugin_data ) {
							self::show_plugin_card( $plugin_data );
						}
						?>

					</div>
				</div>
			<?php
		}

		public static function show_plugin_card( $plugin_data ) {
			if ( ! empty( $plugin_data ) ) {
				?>
				<div class='wrld-plugin-card'>
					<a href="<?php echo esc_attr( $plugin_data['link'] ); ?>" target='__blank' >
						<div class='wrld-plugins-image-container'>
							<img class='wrld-plugins-image' src='<?php echo esc_attr( WRLD_REPORTS_SITE_URL . '/includes/admin/dashboard/assets/images/' . $plugin_data['image'] ); ?>' alt='<?php echo esc_attr( $plugin_data['name'] ); ?>'>
						</div>
						<div class='wrld-plugin-title'>
							<span class='wrld-plugin-title-text'><?php echo esc_html( $plugin_data['name'] ); ?></span>
						</div>
						<div class='wrld-plugin-excerpt'>
							<p><?php echo esc_html( $plugin_data['excerpt'] ); ?></p>
						</div>
						<div class='wrld-plugin-link'>
							<?php esc_html_e( 'Learn More', 'learndash-reports-pro' ); ?>
						</div>
					</a>
				</div>
				<?php
			}
		}
	}
}
