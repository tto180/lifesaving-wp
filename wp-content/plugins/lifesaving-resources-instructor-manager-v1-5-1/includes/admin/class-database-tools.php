<?php
if (!defined('ABSPATH')) exit;

class LSIM_Database_Tools {
    public function __construct() {
        add_action('admin_menu', [$this, 'add_tools_page'], 99);
    }

    public function add_tools_page() {
        add_submenu_page(
            'lifesaving-resources',
            'Database Tools',
            'Database Tools',
            'manage_options',
            'lsim-database-tools',
            [$this, 'render_tools_page']
        );
    }

    public function render_tools_page() {
        // Security check
        if (!current_user_can('manage_options')) {
            wp_die('Unauthorized access');
        }

        $installer = LSIM_Database_Installer::get_instance();
        $issues = $installer->verify_database_integrity();
        $current_version = get_option('lsim_db_version', 'Unknown');
        
        // Handle repair action
        if (isset($_POST['action']) && $_POST['action'] === 'repair_database') {
            check_admin_referer('lsim_repair_database');
            try {
                $installer->install();
                $message = 'Database repaired successfully.';
                $message_type = 'success';
            } catch (Exception $e) {
                $message = 'Database repair failed: ' . $e->getMessage();
                $message_type = 'error';
            }
        }
        ?>
        <div class="wrap">
            <h1>Database Tools</h1>

            <?php if (isset($message)): ?>
                <div class="notice notice-<?php echo $message_type; ?> is-dismissible">
                    <p><?php echo esc_html($message); ?></p>
                </div>
            <?php endif; ?>

            <div class="card">
                <h2>Database Status</h2>
                <table class="widefat">
                    <tr>
                        <td><strong>Database Version:</strong></td>
                        <td><?php echo esc_html($current_version); ?></td>
                    </tr>
                    <tr>
                        <td><strong>Plugin Version:</strong></td>
                        <td><?php echo esc_html(LSIM_VERSION); ?></td>
                    </tr>
                    <?php
                    global $wpdb;
                    $tables = [
                        'lsim_course_history',
                        'lsim_assistant_history',
                        'lsim_pending_assistants'
                    ];
                    
                    foreach ($tables as $table) {
                        $table_name = $wpdb->prefix . $table;
                        $count = $wpdb->get_var("SELECT COUNT(*) FROM $table_name");
                        ?>
                        <tr>
                            <td><strong><?php echo esc_html($table); ?>:</strong></td>
                            <td><?php echo number_format($count); ?> records</td>
                        </tr>
                        <?php
                    }
                    ?>
                </table>
            </div>

            <?php if (empty($issues)): ?>
                <div class="card">
                    <h2>Database Verification</h2>
                    <p class="notice notice-success">
                        ✓ All database tables and columns are correctly configured.
                    </p>
                </div>
            <?php else: ?>
                <div class="card">
                    <h2>Database Issues Detected</h2>
                    <div class="notice notice-error">
                        <p>The following issues were found:</p>
                        <ul>
                            <?php foreach ($issues as $issue): ?>
                                <li>• <?php echo esc_html($issue); ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                    
                    <form method="post">
                        <?php wp_nonce_field('lsim_repair_database'); ?>
                        <input type="hidden" name="action" value="repair_database">
                        <p>
                            <button type="submit" class="button button-primary" onclick="return confirm('Are you sure you want to repair the database? This may take a few moments.');">
                                Repair Database
                            </button>
                        </p>
                    </form>
                </div>
            <?php endif; ?>

            <div class="card">
                <h2>Backup Reminder</h2>
                <p>Before performing any database operations, it's recommended to backup your database.</p>
            </div>
        </div>

        <style>
            .card {
                background: #fff;
                border: 1px solid #ccd0d4;
                border-radius: 4px;
                padding: 20px;
                margin-top: 20px;
                box-shadow: 0 1px 1px rgba(0,0,0,0.04);
            }
            .card h2 {
                margin-top: 0;
                padding-bottom: 10px;
                border-bottom: 1px solid #eee;
            }
            .widefat td {
                padding: 12px;
            }
        </style>
        <?php
    }
}