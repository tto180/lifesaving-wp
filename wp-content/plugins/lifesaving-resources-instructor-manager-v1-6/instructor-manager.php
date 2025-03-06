<?php
/**
 * Plugin Name: Lifesaving Resources Instructor Manager
 * Description: Manages Ice and Water Rescue instructors, certifications, and course completions
 * Version: 1.5.5 - working to improvr data flow from form to plugin
 * Author: MSJ Marketing & Communications
 */

if (!defined('ABSPATH')) {
    exit;
}

// Define plugin constants first, before using them
define('LSIM_VERSION', '1.5.5');
define('LSIM_PLUGIN_FILE', __FILE__);
define('LSIM_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('LSIM_PLUGIN_URL', plugin_dir_url(__FILE__));
define('LSIM_CORE_DIR', LSIM_PLUGIN_DIR . 'includes/core/');
define('LSIM_MIN_PHP_VERSION', '7.4');
define('LSIM_MIN_WP_VERSION', '5.8');
define('LSIM_NONCE_ACTION', 'lsim_nonce');

// Load logger first
	$logger_file = LSIM_CORE_DIR . 'class-logger.php';
		if (file_exists($logger_file)) {
			require_once $logger_file;
		} else {
			error_log("LSIM: Critical Error - Logger file missing");
			wp_die('Plugin could not be initialized: Logger file missing');
		}

		// Load remaining core files
		$core_files = [
			'class-data-manager.php',
			'class-database-installer.php'
		];

		foreach ($core_files as $file) {
			$filepath = LSIM_CORE_DIR . $file;
			if (file_exists($filepath)) {
				require_once $filepath;
			} else {
				lsim_log_error("Missing core file", $file);
			}
		}
		
		lsim_log_info('Plugin version info', [
			'version' => LSIM_VERSION,
			'php_version' => PHP_VERSION,
			'wp_version' => get_bloginfo('version')
		]);

// Define activation/deactivation functions before registering hooks
	function lsim_activate() {
		try {
			lsim_log_info('Plugin activation started');  // Add this line
			$installer = LSIM_Database_Installer::get_instance();
			$installer->install();
			lsim_log_info('Plugin activation completed successfully');  // Add this line
		} catch (Exception $e) {
			lsim_log_error('Plugin activation failed', $e->getMessage());  // Add this line
			wp_die('Failed to install plugin: ' . $e->getMessage());
		}
	}

	function lsim_deactivate() {
		// Only clean up transients and scheduled tasks
		wp_clear_scheduled_hook('lsim_pending_cleanup');
		
		global $wpdb;
		$wpdb->query(
			"DELETE FROM `$wpdb->options` 
			WHERE `option_name` LIKE ('_transient_lsim_admin_notices_%') 
			OR `option_name` LIKE ('_transient_timeout_lsim_admin_notices_%')"
		);
	}

	function lsim_uninstall() {
		if (!defined('WP_UNINSTALL_PLUGIN')) {
			exit;
		}

		try {
			$installer = LSIM_Database_Installer::get_instance();
			$installer->uninstall();
		} catch (Exception $e) {
			lsim_log_error('Failed to uninstall plugin', $e->getMessage());  // Changed this line
		}
	}

// Register activation and deactivation hooks
	register_activation_hook(LSIM_PLUGIN_FILE, 'lsim_activate');
	register_deactivation_hook(LSIM_PLUGIN_FILE, 'lsim_deactivate');

// Add admin notices handler
add_action('admin_notices', function() {
    $screen = get_current_screen();
    if ($screen && $screen->post_type === 'instructor') {
        $user_id = get_current_user_id();
        $notices = get_transient('lsim_admin_notices_' . $user_id);
        if ($notices) {
            foreach ($notices as $notice) {
                printf(
                    '<div class="notice notice-%s is-dismissible"><p>%s</p></div>',
                    esc_attr($notice['type']),
                    esc_html($notice['message'])
                );
            }
            delete_transient('lsim_admin_notices_' . $user_id);
        }
    }
});

// Consolidated initialization
	add_action('plugins_loaded', function() {
		if (!defined('WP_INSTALLING') && !defined('LSIM_INITIALIZED')) {
			define('LSIM_INITIALIZED', true);
			lsim_log_debug('Plugin initialization started');  // Add this line
			debug_lsim_tables();
			Lifesaving_Resources_Manager::get_instance();
			lsim_log_debug('Plugin initialization completed');  // Add this line
		}
	}, 10);

	function debug_lsim_tables() {
		if (isset($_GET['action']) && $_GET['action'] === 'activate') {
			return;
		}
		
		global $wpdb;
		
		$tables = [
			$wpdb->prefix . 'lsim_course_history',
			$wpdb->prefix . 'lsim_assistant_history',
			$wpdb->prefix . 'lsim_pending_assistants'
		];
		
		foreach ($tables as $table) {
			$exists = $wpdb->get_var("SHOW TABLES LIKE '$table'");
			lsim_log_debug("Table Check - $table: " . ($exists ? 'exists' : 'missing'));
			
			if ($exists) {
				$columns = $wpdb->get_results("DESCRIBE $table");
				lsim_log_debug("Table Columns - $table", $columns);
			}
		}
		
		$count = $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->posts} WHERE post_type = 'instructor'");
		lsim_log_debug("Instructor Count", $count);
	}

	class Lifesaving_Resources_Manager {
		private static $instance = null;
		private $instructor_fields;
		private $form_integration;
		private $import_export; 
		private $notifications;
		private $reporting;
		private $instructor_id;
		private $assistant_tracking;
		private $email_instructors;
		private $instructor_history;

		public static function get_instance() {
			if (null === self::$instance) {
				self::$instance = new self();
			}
			return self::$instance;
		}

	private function load_dependencies() {
		$files = [
			'includes/class-instructor-fields.php',
			'includes/class-form-integration.php',
			'includes/class-import-export.php',
			'includes/class-instructor-history.php',
			'includes/class-notifications.php',
			'includes/class-reporting.php',
			'includes/class-instructor-id.php',
			'includes/class-assistant-tracking.php',
			'includes/class-email-instructors.php',
			'includes/class-unrecognized-handler.php',  // Add this line
			'includes/admin/class-database-tools.php',
			'includes/admin/settings.php'
		];
		foreach ($files as $file) {
			$filepath = LSIM_PLUGIN_DIR . $file;
			if (!file_exists($filepath)) {
				lsim_log_error("Missing required file", $filepath);
				continue;
			}
			require_once $filepath;
		}
	}

	private function __construct() {
		$this->load_dependencies();
		
		// Initialize components
		$this->instructor_fields = new LSIM_Instructor_Fields();
		$this->form_integration = new LSIM_Form_Integration();
		$this->import_export = new LSIM_Import_Export();
		$this->notifications = new LSIM_Notifications();
		$this->reporting = new LSIM_Reporting();
		$this->instructor_id = new LSIM_Instructor_ID();
		$this->assistant_tracking = new LSIM_Assistant_Tracking();
		$this->email_instructors = new LSIM_Email_Instructors();
		$this->instructor_history = new LSIM_Instructor_History();
		$this->database_tools = new LSIM_Database_Tools();
		$this->unrecognized_handler = new LSIM_Unrecognized_Handler();  // Add this line
		
		// Add hooks
		add_action('init', [$this, 'register_post_type']);
		add_action('admin_menu', [$this, 'add_admin_menu']);
		add_action('admin_enqueue_scripts', [$this, 'enqueue_admin_assets']);
		add_action('admin_init', [$this, 'check_dependencies']);
	}

public function register_post_type() {
        register_post_type('instructor', [
            'labels' => [
                'name' => 'Instructors',
                'singular_name' => 'Instructor',
                'add_new' => 'Add New Instructor',
                'add_new_item' => 'Add New Instructor',
                'edit_item' => 'Edit Instructor',
                'view_item' => 'View Instructor',
                'search_items' => 'Search Instructors',
                'not_found' => 'No instructors found',
                'not_found_in_trash' => 'No instructors found in trash'
            ],
            'public' => false,
            'show_ui' => true,
            'show_in_menu' => false,
            'menu_position' => null,
            'supports' => ['title'],
            'capability_type' => 'post',
            'hierarchical' => false,
            'has_archive' => false,
            'show_in_rest' => false,
            'publicly_queryable' => false
        ]);

        register_taxonomy('certification_type', 'instructor', [
            'labels' => [
                'name' => 'Certification Types',
                'singular_name' => 'Certification Type',
                'search_items' => 'Search Certification Types',
                'all_items' => 'All Certification Types',
                'edit_item' => 'Edit Certification Type',
                'update_item' => 'Update Certification Type',
                'add_new_item' => 'Add New Certification Type',
                'new_item_name' => 'New Certification Type Name',
                'menu_name' => 'Certification Types'
            ],
            'hierarchical' => true,
            'show_ui' => false,
            'show_admin_column' => false,
            'query_var' => true,
            'rewrite' => ['slug' => 'certification-type']
        ]);
    }

    public function add_admin_menu() {
        add_menu_page(
            'Lifesaving Resources',
            'Lifesaving Resources',
            'manage_options',
            'lifesaving-resources',
            [$this, 'render_dashboard_page'],
            'dashicons-shield',
            30
        );

        add_submenu_page(
            'lifesaving-resources',
            'Dashboard',
            'Dashboard',
            'manage_options',
            'lifesaving-resources',
            [$this, 'render_dashboard_page']
        );

        add_submenu_page(
            'lifesaving-resources',
            'Instructors',
            'Instructors',
            'manage_options',
            'edit.php?post_type=instructor'
        );

        add_submenu_page(
            'lifesaving-resources',
            'Add Instructor',
            'Add Instructor',
            'manage_options',
            'post-new.php?post_type=instructor'
        );

        add_submenu_page(
            'lifesaving-resources',
            'Reports',
            'Reports',
            'manage_options',
            'instructor-reports',
            [$this->reporting, 'render_reports_page']
        );

        add_submenu_page(
            'lifesaving-resources',
            'Email Instructors',
            'Email Instructors',
            'manage_options',
            'email-instructors',
            [$this->email_instructors, 'render_email_page']
        );

        add_submenu_page(
            'lifesaving-resources',
            'Import/Export',
            'Import/Export',
            'manage_options',
            'instructor-import-export',
            [$this->import_export, 'render_import_export_page']
        );

        $settings_page = new LSIM_Admin_Settings();
        add_submenu_page(
            'lifesaving-resources',
            'Settings',
            'Settings',
            'manage_options',
            'instructor-settings',
            [$settings_page, 'render_settings_page']
        );
    }

    public function enqueue_admin_assets($hook) {
        if (!$this->is_plugin_page($hook)) {
            return;
        }

        wp_enqueue_style(
            'lsim-admin-style',
            LSIM_PLUGIN_URL . 'assets/css/admin-styles.css',
            [],
            LSIM_VERSION
        );

        wp_enqueue_script(
            'lsim-admin-script',
            LSIM_PLUGIN_URL . 'assets/js/admin-scripts.js',
            ['jquery'],
            LSIM_VERSION,
            true
        );

        wp_localize_script('lsim-admin-script', 'lsimVars', [
            'ajaxurl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce(LSIM_NONCE_ACTION),
            'pluginUrl' => LSIM_PLUGIN_URL
        ]);

        if ($this->is_reports_page($hook)) {
            $this->enqueue_report_assets();
        }
    }

	public function check_dependencies() {
		$notices = [];
		if (!class_exists('GFAPI')) {
			$notices[] = 'Gravity Forms is required for full functionality.';
			lsim_log_error('Dependency check failed', 'Gravity Forms not found');
		}
		
		if (version_compare(PHP_VERSION, LSIM_MIN_PHP_VERSION, '<')) {
			$notices[] = sprintf('PHP version %s or higher is required.', LSIM_MIN_PHP_VERSION);
			lsim_log_error('Dependency check failed', [
				'required_php' => LSIM_MIN_PHP_VERSION,
				'current_php' => PHP_VERSION
			]);
		}
        
        if (!empty($notices)) {
            add_action('admin_notices', function() use ($notices) {
                foreach ($notices as $notice) {
                    printf('<div class="notice notice-error"><p>%s</p></div>', esc_html($notice));
                }
            });
        }
    }

    public function render_dashboard_page() {
        $nonce = wp_create_nonce('lsim_dashboard_nonce');
        $ice_instructors = $this->get_active_instructors('ice');
        $water_instructors = $this->get_active_instructors('water');
        $recent_courses = $this->get_recent_courses(5);
        require_once LSIM_PLUGIN_DIR . 'includes/admin/templates/dashboard.php';
    }

    private function get_active_instructors($type) {
        return get_posts([
            'post_type' => 'instructor',
            'posts_per_page' => -1,
            'meta_query' => [
                [
                    'key' => "_{$type}_active",
                    'value' => '1'
                ]
            ]
        ]);
    }

    private function get_recent_courses($limit = 5) {
        global $wpdb;
        return $wpdb->get_results(
            $wpdb->prepare(
                "SELECT ch.*, p.post_title as instructor_name 
                FROM {$wpdb->prefix}lsim_course_history ch 
                JOIN {$wpdb->posts} p ON ch.instructor_id = p.ID 
                ORDER BY ch.course_date DESC LIMIT %d",
                $limit
            )
        );
    }

    public function is_plugin_page($hook) {
        $screen = get_current_screen();
        return strpos($hook, 'lifesaving-resources') !== false || 
               ($screen && $screen->post_type === 'instructor');
    }

    public function is_reports_page($hook) {
        return strpos($hook, 'instructor-reports') !== false;
    }

    private function enqueue_report_assets() {
        wp_enqueue_style(
            'lsim-report-style',
            LSIM_PLUGIN_URL . 'assets/css/report-styles.css',
            [],
            LSIM_VERSION
        );

        wp_enqueue_script(
            'lsim-report-script',
            LSIM_PLUGIN_URL . 'assets/js/reporting-scripts.js',
            ['jquery'],
            LSIM_VERSION,
            true
        );
    }
}

	// AJAX handler for saving instructors
	add_action('wp_ajax_save_instructor', function() {
		check_ajax_referer(LSIM_NONCE_ACTION, 'nonce');
		
		if (!current_user_can('edit_posts')) {
			wp_send_json_error(['message' => 'Unauthorized access']);
			return;
		}
		
		if (empty($_POST['form_data'])) {
			wp_send_json_error(['message' => 'No form data received']);
			return;
		}
		
		parse_str($_POST['form_data'], $form_data);
		
		lsim_log_debug('Instructor save initiated', [
			'instructor_data' => $form_data
		]);
		
		try {
			$data_manager = LSIM_Data_Manager::get_instance();
			
			// Prepare instructor data
			$instructor_data = [
				'first_name' => sanitize_text_field($form_data['first_name'] ?? ''),
				'last_name' => sanitize_text_field($form_data['last_name'] ?? ''),
				'email' => sanitize_email($form_data['email'] ?? ''),
				'phone' => sanitize_text_field($form_data['phone'] ?? ''),
				'department' => sanitize_text_field($form_data['department'] ?? ''),
				'state' => sanitize_text_field($form_data['state'] ?? ''),
				// Add certification data
				'ice_original_date' => sanitize_text_field($form_data['ice_original_date'] ?? ''),
				'ice_recert_dates' => isset($form_data['ice_recert_dates']) ? array_map('sanitize_text_field', $form_data['ice_recert_dates']) : [],
				'water_original_date' => sanitize_text_field($form_data['water_original_date'] ?? ''),
				'water_recert_dates' => isset($form_data['water_recert_dates']) ? array_map('sanitize_text_field', $form_data['water_recert_dates']) : []
			];

			// Add ID if we're updating
			if (!empty($form_data['post_ID'])) {
				$instructor_data['ID'] = intval($form_data['post_ID']);
			}

			// Save instructor
			$instructor_id = $data_manager->save_instructor($instructor_data);

			// Save certification data
			foreach (['ice', 'water'] as $type) {
				if (!empty($instructor_data["{$type}_original_date"])) {
					update_post_meta($instructor_id, "_{$type}_original_date", $instructor_data["{$type}_original_date"]);
				}
				
				if (!empty($instructor_data["{$type}_recert_dates"])) {
					$dates = array_filter($instructor_data["{$type}_recert_dates"]);
					sort($dates);
					update_post_meta($instructor_id, "_{$type}_recert_dates", $dates);
				}
			}

			wp_send_json_success([
				'instructor_id' => $instructor_id,
				'message' => 'Instructor saved successfully'
			]);

		} catch (Exception $e) {
			lsim_log_error('Instructor save error', [
				'error' => $e->getMessage(),
				'data' => $form_data
			]);
			
			wp_send_json_error([
				'message' => $e->getMessage(),
				'type' => $e->getMessage() === 'Email already exists' ? 'duplicate_email' : 'error'
			]);
		}
	});

	// AJAX handler for saving courses
	add_action('wp_ajax_save_course', function() {
		check_ajax_referer(LSIM_NONCE_ACTION, 'nonce');
		
		if (!current_user_can('edit_posts')) {
			wp_send_json_error(['message' => 'Unauthorized access']);
			return;
		}
		
		lsim_log_debug('Course save initiated', [
			'course_data' => $course_data
		]);

		try {
			$data_manager = LSIM_Data_Manager::get_instance();
			
			// Prepare course data
			$course_data = [
				'instructor_id' => intval($_POST['instructor_id']),
				'course_type' => sanitize_text_field($_POST['course_type']),
				'course_date' => sanitize_text_field($_POST['course_date']),
				'location' => sanitize_text_field($_POST['location'])
			];

			// Add course ID if updating
			if (!empty($_POST['course_id'])) {
				$course_data['course_id'] = intval($_POST['course_id']);
			}

			// Parse participants data
			if (!empty($_POST['participants_data'])) {
				$participants = json_decode(stripslashes($_POST['participants_data']), true);
				if (json_last_error() !== JSON_ERROR_NONE) {
					throw new Exception('Invalid participants data format');
				}
				$course_data['participants_data'] = $participants;
			}

			// Parse assistants data
			if (!empty($_POST['assistants']) && is_array($_POST['assistants'])) {
				$assistants = array_map(function($assistant) {
					return [
						'first_name' => sanitize_text_field($assistant['first_name'] ?? ''),
						'last_name' => sanitize_text_field($assistant['last_name'] ?? ''),
						'email' => sanitize_email($assistant['email'] ?? '')
					];
				}, $_POST['assistants']);
				$course_data['assistants'] = $assistants;
			}

			// Save course
			$course_id = $data_manager->save_course($course_data);

			wp_send_json_success([
				'course_id' => $course_id,
				'message' => 'Course saved successfully'
			]);

		} catch (Exception $e) {
			lsim_log_error('Course save error', [
				'error' => $e->getMessage(),
				'data' => $_POST
			]);
			
			wp_send_json_error([
				'message' => $e->getMessage()
			]);
		}
	});
	
	// Add AJAX handler for form field verification
	add_action('wp_ajax_verify_form_fields', function() {
		check_ajax_referer('verify_form_fields', 'nonce');
		
		if (!current_user_can('manage_options')) {
			wp_send_json_error(['message' => 'Unauthorized access']);
			return;
		}

		$form_id = intval($_POST['form_id']);
		$form_type = sanitize_text_field($_POST['form_type']);
		
		// Check if Gravity Forms is active
		if (!class_exists('GFAPI')) {
			wp_send_json_error(['message' => 'Gravity Forms is not active']);
			return;
		}

		// Get the form
		$form = GFAPI::get_form($form_id);
		if (!$form) {
			wp_send_json_error(['message' => "Form ID $form_id not found"]);
			return;
		}

		// Get the field mappings from the submission
		parse_str($_POST['fields'], $fields);
		$field_mappings = $fields['lsim_form_field_mappings'][$form_type] ?? [];

		// Verify each field exists in the form
		$missing_fields = [];
		foreach ($field_mappings as $field_name => $field_id) {
			$field_parts = explode('.', $field_id);
			$base_field_id = intval($field_parts[0]);
			
			$field = GFAPI::get_field($form, $base_field_id);
			if (!$field) {
				$missing_fields[] = "Field ID $field_id ($field_name) not found in form";
			}
		}

		if (!empty($missing_fields)) {
			wp_send_json_error([
				'message' => "The following fields were not found:\n" . implode("\n", $missing_fields)
			]);
			return;
		}

		wp_send_json_success(['message' => 'All fields verified successfully']);
	});	

// Add periodic cleanup of pending assistants
add_action('admin_init', function() {
    // Run cleanup once per day
    if (get_transient('lsim_pending_cleanup') !== false) {
        return;
    }
    
    global $wpdb;
    
    // Get all instructors with their emails
    $instructors = get_posts([
        'post_type' => 'instructor',
        'posts_per_page' => -1,
        'meta_query' => [
            [
                'key' => '_email',
                'compare' => 'EXISTS'
            ]
        ]
    ]);

    $instructor_emails = [];
    foreach ($instructors as $instructor) {
        $email = get_post_meta($instructor->ID, '_email', true);
        if ($email) {
            $instructor_emails[] = $email;
            
            // Convert any pending records for this instructor
            $pending = $wpdb->get_results($wpdb->prepare(
                "SELECT * FROM {$wpdb->prefix}lsim_pending_assistants 
                WHERE email = %s",
                $email
            ));
            
            foreach ($pending as $record) {
                $wpdb->insert(
                    $wpdb->prefix . 'lsim_assistant_history',
                    [
                        'instructor_id' => $instructor->ID,
                        'course_id' => $record->course_id,
                        'created_at' => current_time('mysql')
                    ],
                    ['%d', '%d', '%s']
                );
            }
            
            if (!empty($pending)) {
				$wpdb->delete(
					$wpdb->prefix . 'lsim_pending_assistants',
					['email' => $email],
					['%s']
				);
				lsim_log_info('Cleanup converted pending records', [
					'count' => count($pending),
					'instructor_id' => $instructor->ID,
					'email' => $email
				]);
						}
        }
    }
    
    // Set transient to prevent running too often
    set_transient('lsim_pending_cleanup', true, DAY_IN_SECONDS);
});

// Remove footer text
function lsim_remove_footer_admin() {
    echo '';
}

// Remove all footer hooks on instructor pages
add_action('admin_init', function() {
    $screen = get_current_screen();
    if ($screen && $screen->post_type === 'instructor') {
        remove_all_filters('admin_footer_text');
        remove_all_filters('update_footer');
        add_filter('admin_footer_text', 'lsim_remove_footer_admin', 99);
        add_filter('update_footer', 'lsim_remove_footer_admin', 99);
        remove_action('in_admin_footer', 'wp_admin_community_events');
    }
});

// Override screen options text
add_action('admin_head', function() {
    if (get_current_screen()->post_type === 'instructor') {
        echo '<style>#wpfooter { display: none !important; }</style>';
    }
});