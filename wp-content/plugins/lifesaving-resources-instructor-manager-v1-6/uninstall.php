<?php
// If uninstall not called from WordPress, then exit
if (!defined('WP_UNINSTALL_PLUGIN')) {
    exit;
}

// Ensure we have access to WordPress functions
require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

function lsim_should_preserve_data() {
    $settings = get_option('lsim_settings', []);
    $preserve = isset($settings['preserve_data_on_uninstall']) && $settings['preserve_data_on_uninstall'];
    error_log('LSIM: Data preservation setting: ' . ($preserve ? 'enabled' : 'disabled'));
    return $preserve;
}

function lsim_complete_uninstall() {
    global $wpdb;
    
    $preserve_data = lsim_should_preserve_data();
    
    if (!$preserve_data) {
        error_log('LSIM: Starting complete uninstall process');
        
        // Count instructors before deletion
        $instructors = get_posts([
            'post_type' => 'instructor',
            'numberposts' => -1,
            'post_status' => 'any',
            'fields' => 'ids'
        ]);
        
        $count = count($instructors);
        error_log("LSIM: Removing $count instructor records");

        foreach ($instructors as $instructor_id) {
            wp_delete_post($instructor_id, true);
        }

        // Remove tables and log results
        $tables = [
            $wpdb->prefix . 'lsim_course_history',
            $wpdb->prefix . 'lsim_assistant_history',
            $wpdb->prefix . 'lsim_pending_assistants'
        ];

        foreach ($tables as $table) {
            $exists = $wpdb->get_var("SHOW TABLES LIKE '$table'");
            if ($exists) {
                $wpdb->query("DROP TABLE IF EXISTS `$table`");
                error_log("LSIM: Removed table - $table");
            }
        }

        // Clean up options
        delete_option('lsim_settings');
        delete_option('lsim_db_version');
        delete_option('lsim_unrecognized_submissions');
        
        error_log('LSIM: Complete uninstall finished - all data removed');
    } else {
        error_log('LSIM: Starting partial uninstall (preserving data)');
        delete_option('lsim_db_version');
        error_log('LSIM: Partial uninstall completed - data preserved');
    }

    // Always perform these actions
    wp_clear_scheduled_hook('lsim_pending_cleanup');
    flush_rewrite_rules();
}

// Run the uninstall function
lsim_complete_uninstall();