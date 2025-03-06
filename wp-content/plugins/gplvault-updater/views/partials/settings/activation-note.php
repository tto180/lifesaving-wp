<?php
// phpcs:ignoreFile
defined( 'ABSPATH' ) || exit;
/**
 * @var array   $settings_url
 */
?>
<div class="gv-layout__columns">
	<div class="gv-card">
		<div class="gv-card__body">
			<div class="gv-card__body-inner gv-justify-center">
				<div>
					<h3 class="text-center"><?php esc_html_e('Please, activation your license.', 'gplvault'); ?></h3>
					<a class="button button-primary" href="<?php echo esc_url($settings_url); ?>">Go to Settings</a>
				</div>
			</div>
		</div>
	</div>
</div>
<?php
