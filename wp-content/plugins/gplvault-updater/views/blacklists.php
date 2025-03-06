<?php
defined( 'ABSPATH' ) || exit;

/** @var GPLVault_Settings_Manager $settings_manager */
//$api_settings         = $settings_manager->get_api_settings();
$blocked_plugins      = $settings_manager->blocked_plugins();
$blocked_themes       = $settings_manager->blocked_themes();
$is_license_activated = $settings_manager->license_is_activated();
$settings_url 		  = GPLVault_Admin::admin_links('settings');
//$gv_license_key       = $api_settings[ GPLVault_Settings_Manager::API_KEY ] ?? '';
//$gv_product_id        = $api_settings[ GPLVault_Settings_Manager::PRODUCT_KEY ] ?? '';
//$license_status_class = $is_license_activated ? 'gv-status__success' : 'gv-status__error';

//$license_summary = $is_license_activated ? $settings_manager->license_status() : array();
?>
	<div class="wrap gv-wrapper" id="gv_settings_wrapper">
		<div class="gv-layout">
			<div class="gv-layout__primary">
				<div class="gv-layout__main gv-grids gv-grids__full">

					<div class="gv-admin-section" id="gv_items_exclusion">
						<?php if ($is_license_activated) : ?>
						<div class="gv-section-header">
							<div>
								<h2 class="gv-section-header__title"><?php esc_html_e( 'Disable Automatic Updates', 'gplvault' ); ?></h2>
								<div><?php esc_html_e('You can choose not to update certain plugins or themes from GPL Vault. This is useful if you have a direct license from the developer and prefer getting updates from them instead of GPL Vault.', 'gplvault'); ?></div>
							</div>
						</div>
						<div class="gv-admin-columns gv-grids gv-grids__columns-auto">
							<?php
							GPLVault_Admin::load_partial( 'settings/blocked-plugins', compact( 'blocked_plugins' ) );
							?>
							<?php
							GPLVault_Admin::load_partial( 'settings/blocked-themes', compact( 'blocked_themes' ) );
							?>
						</div>
						<?php else : ?>
						<div class="gv-admin-columns gv-grids gv-grids__columns-auto">
							<?php
							GPLVault_Admin::load_partial( 'settings/activation-note', compact( 'settings_url' ) );
							?>
						</div>
						<?php endif; ?>
					</div>
				</div>
			</div>
		</div>
	</div> <!-- .wrap -->
<?php
