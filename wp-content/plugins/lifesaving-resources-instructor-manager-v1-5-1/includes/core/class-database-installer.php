<?php
if (!defined('ABSPATH')) exit;

class LSIM_Database_Installer {
    private static $instance = null;
    private $wpdb;
    
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        global $wpdb;
        $this->wpdb = $wpdb;
    }

    public function install() {
        try {
            $this->create_tables();
            $this->add_default_data();
            update_option('lsim_db_version', LSIM_VERSION);
            lsim_log_info('Database installation completed successfully');
        } catch (Exception $e) {
            lsim_log_error('Database installation failed', $e->getMessage());
            throw $e;
        }
    }

    private function create_tables() {
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        $charset_collate = $this->wpdb->get_charset_collate();

        // Course History Table
        $sql = "CREATE TABLE IF NOT EXISTS {$this->wpdb->prefix}lsim_course_history (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            instructor_id bigint(20) NOT NULL,
            course_type varchar(50) NOT NULL,
            course_date date NOT NULL,
            location text NOT NULL,
            participants_data text NOT NULL,
            hours int(11) NOT NULL DEFAULT 0,
            form_entry_id bigint(20) DEFAULT NULL,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            modified_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY  (id),
            KEY instructor_id (instructor_id),
            KEY course_date (course_date),
            KEY course_type (course_type)
        ) $charset_collate;";
        
        dbDelta($sql);
        $this->verify_table_exists('lsim_course_history');

        // Assistant History Table
        $sql = "CREATE TABLE IF NOT EXISTS {$this->wpdb->prefix}lsim_assistant_history (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            instructor_id bigint(20) NOT NULL,
            course_id bigint(20) NOT NULL,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            modified_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY instructor_id (instructor_id),
            KEY course_id (course_id)
        ) $charset_collate;";

        dbDelta($sql);
        $this->verify_table_exists('lsim_assistant_history');

        // Pending Assistants Table
        $sql = "CREATE TABLE IF NOT EXISTS {$this->wpdb->prefix}lsim_pending_assistants (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            course_id bigint(20) NOT NULL,
            first_name varchar(255) NOT NULL,
            last_name varchar(255) NOT NULL,
            email varchar(255) NOT NULL,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            modified_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY course_id (course_id),
            KEY email (email)
        ) $charset_collate;";

        dbDelta($sql);
        $this->verify_table_exists('lsim_pending_assistants');
    }

    private function verify_table_exists($table_name) {
        $full_table_name = $this->wpdb->prefix . $table_name;
        if (!$this->wpdb->get_var("SHOW TABLES LIKE '$full_table_name'")) {
            throw new Exception("Failed to create table: $table_name");
        }
    }

    private function add_default_data() {
        // Add default certification types if they don't exist
        if (!term_exists('Ice Rescue', 'certification_type')) {
            wp_insert_term('Ice Rescue', 'certification_type');
        }
        if (!term_exists('Water Rescue', 'certification_type')) {
            wp_insert_term('Water Rescue', 'certification_type');
        }

        // Add default settings if they don't exist
        if (!get_option('lsim_settings')) {
            update_option('lsim_settings', [
                'require_phone' => true,
                'require_department' => true,
                'notification_email' => get_option('admin_email')
            ]);
        }
    }

    public function uninstall() {
        try {
            $this->cleanup_database();
            delete_option('lsim_db_version');
            delete_option('lsim_settings');
            lsim_log_info('Database uninstallation completed successfully');
        } catch (Exception $e) {
            lsim_log_error('Database uninstallation failed', $e->getMessage());
            throw $e;
        }
    }

    private function cleanup_database() {
        $tables = [
            'lsim_course_history',
            'lsim_assistant_history',
            'lsim_pending_assistants'
        ];

        foreach ($tables as $table) {
            $this->wpdb->query("DROP TABLE IF EXISTS {$this->wpdb->prefix}$table");
        }

        // Clean up all plugin-related post meta
        $this->wpdb->query("DELETE FROM {$this->wpdb->postmeta} WHERE meta_key LIKE '\_lsim\_%'");
        
        // Delete all instructor posts
        $this->wpdb->query(
            $this->wpdb->prepare(
                "DELETE FROM {$this->wpdb->posts} WHERE post_type = %s",
                'instructor'
            )
        );

        // Clean up taxonomy terms
        $terms = get_terms([
            'taxonomy' => 'certification_type',
            'hide_empty' => false
        ]);

        foreach ($terms as $term) {
            wp_delete_term($term->term_id, 'certification_type');
        }
    }

    public function verify_database_integrity() {
        $issues = [];
        
        // Check tables exist and have correct structure
        $expected_tables = [
            'lsim_course_history' => [
                'instructor_id', 'course_type', 'course_date', 'location',
                'participants_data', 'hours', 'form_entry_id'
            ],
            'lsim_assistant_history' => [
                'instructor_id', 'course_id'
            ],
            'lsim_pending_assistants' => [
                'course_id', 'first_name', 'last_name', 'email'
            ]
        ];

        foreach ($expected_tables as $table => $required_columns) {
            $table_name = $this->wpdb->prefix . $table;
            
            if (!$this->wpdb->get_var("SHOW TABLES LIKE '$table_name'")) {
                $issues[] = "Missing table: $table";
                continue;
            }

            $columns = $this->wpdb->get_results("DESCRIBE $table_name");
            $column_names = array_map(function($col) {
                return $col->Field;
            }, $columns);

            foreach ($required_columns as $column) {
                if (!in_array($column, $column_names)) {
                    $issues[] = "Missing column $column in table $table";
                }
            }
        }

        return $issues;
    }
}