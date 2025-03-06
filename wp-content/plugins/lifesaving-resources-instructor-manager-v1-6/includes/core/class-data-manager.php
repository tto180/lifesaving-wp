<?php
if (!defined('ABSPATH')) exit;

class LSIM_Data_Manager {
    private static $instance = null;
    private $wpdb;
    private $last_error = null;

    // Validation rules as class constants
    const REQUIRED_INSTRUCTOR_FIELDS = ['first_name', 'last_name', 'email'];
    const REQUIRED_COURSE_FIELDS = ['instructor_id', 'course_type', 'course_date', 'location'];
    const ALLOWED_COURSE_TYPES = ['ice', 'water'];
    const MAX_ASSISTANT_COUNT = 3;

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

    public function get_last_error() {
        return $this->last_error;
    }

    private function set_error($message, $data = null) {
        $this->last_error = $message;
        lsim_log_error($message, $data);
    }

    // Instructor Operations
    public function get_instructor_by_email($email) {
        try {
            $instructors = get_posts([
                'post_type' => 'instructor',
                'meta_key' => '_email',
                'meta_value' => $email,
                'posts_per_page' => 1
            ]);
            
            lsim_log_debug('Instructor lookup by email', [
                'email' => $email,
                'found' => !empty($instructors)
            ]);

            return $instructors;
        } catch (Exception $e) {
            $this->set_error('Error looking up instructor', [
                'email' => $email,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    public function save_instructor($data) {
        try {
            $this->wpdb->query('START TRANSACTION');

            // Validate data
            $this->validate_instructor_data($data);

            // Check for duplicate email
            $existing = $this->get_instructor_by_email($data['email']);
            if ($existing && (!isset($data['ID']) || $existing[0]->ID != $data['ID'])) {
                throw new Exception('Email already exists');
            }

            // Prepare post data
            $post_data = [
                'post_type' => 'instructor',
                'post_status' => 'publish',
                'post_title' => $data['last_name'] . ', ' . $data['first_name']
            ];

            if (isset($data['ID'])) {
                $post_data['ID'] = $data['ID'];
            }

            // Insert or update post
            $post_id = wp_insert_post($post_data, true);
            if (is_wp_error($post_id)) {
                throw new Exception($post_id->get_error_message());
            }

            // Save meta fields
            $meta_fields = ['first_name', 'last_name', 'email', 'phone', 'department', 'state'];
            foreach ($meta_fields as $field) {
                if (isset($data[$field])) {
                    update_post_meta($post_id, '_' . $field, sanitize_text_field($data[$field]));
                }
            }

            $this->wpdb->query('COMMIT');
            
            lsim_log_info('Instructor saved successfully', [
                'instructor_id' => $post_id,
                'email' => $data['email']
            ]);
            
            return $post_id;

        } catch (Exception $e) {
            $this->wpdb->query('ROLLBACK');
            $this->set_error('Error saving instructor', [
                'error' => $e->getMessage(),
                'data' => $data
            ]);
            throw $e;
        }
    }

    // Course Operations
    public function save_course($data) {
        try {
            $this->wpdb->query('START TRANSACTION');

            // Validate course data
            $this->validate_course_data($data);

            // If assistants provided, validate them
            if (!empty($data['assistants'])) {
                $this->validate_assistant_data($data['assistants']);
            }

            $course_data = [
                'instructor_id' => $data['instructor_id'],
                'course_type' => $data['course_type'],
                'course_date' => $data['course_date'],
                'location' => $data['location'],
                'participants_data' => wp_json_encode($data['participants_data']),
                'modified_at' => current_time('mysql')
            ];

            if (!empty($data['course_id'])) {
                // Update existing course
                $result = $this->wpdb->update(
                    $this->wpdb->prefix . 'lsim_course_history',
                    $course_data,
                    ['id' => $data['course_id']]
                );
            } else {
                // Insert new course
                $course_data['created_at'] = current_time('mysql');
                $result = $this->wpdb->insert(
                    $this->wpdb->prefix . 'lsim_course_history',
                    $course_data
                );
            }

            if ($result === false) {
                throw new Exception($this->wpdb->last_error ?: 'Database error occurred');
            }

            $course_id = !empty($data['course_id']) ? $data['course_id'] : $this->wpdb->insert_id;

            // Process assistants if any
            if (!empty($data['assistants'])) {
                $this->process_course_assistants($course_id, $data['assistants']);
            }

            $this->wpdb->query('COMMIT');
            
            lsim_log_info('Course saved successfully', [
                'course_id' => $course_id,
                'instructor_id' => $data['instructor_id']
            ]);
            
            return $course_id;

        } catch (Exception $e) {
            $this->wpdb->query('ROLLBACK');
            $this->set_error('Error saving course', [
                'error' => $e->getMessage(),
                'data' => $data
            ]);
            throw $e;
        }
    }

    private function process_course_assistants($course_id, $assistants) {
        // Clear existing assistant records
        $this->wpdb->delete(
            $this->wpdb->prefix . 'lsim_assistant_history',
            ['course_id' => $course_id]
        );
        
        $this->wpdb->delete(
            $this->wpdb->prefix . 'lsim_pending_assistants',
            ['course_id' => $course_id]
        );

        foreach ($assistants as $assistant) {
            if (empty($assistant['email'])) continue;

            $instructor = $this->get_instructor_by_email($assistant['email']);
            
            if ($instructor) {
                $this->wpdb->insert(
                    $this->wpdb->prefix . 'lsim_assistant_history',
                    [
                        'instructor_id' => $instructor[0]->ID,
                        'course_id' => $course_id,
                        'created_at' => current_time('mysql')
                    ]
                );
            } else {
                $this->wpdb->insert(
                    $this->wpdb->prefix . 'lsim_pending_assistants',
                    [
                        'course_id' => $course_id,
                        'first_name' => sanitize_text_field($assistant['first_name']),
                        'last_name' => sanitize_text_field($assistant['last_name']),
                        'email' => sanitize_email($assistant['email']),
                        'created_at' => current_time('mysql')
                    ]
                );
            }
        }
    }

    // Validation Methods
    private function validate_instructor_data($data) {
        foreach (self::REQUIRED_INSTRUCTOR_FIELDS as $field) {
            if (empty($data[$field])) {
                throw new Exception("Missing required field: $field");
            }
        }

        if (!is_email($data['email'])) {
            throw new Exception("Invalid email format: {$data['email']}");
        }

        if (!empty($data['phone']) && !preg_match('/^[\d\s\-\(\)\.]+$/', $data['phone'])) {
            throw new Exception("Invalid phone format: {$data['phone']}");
        }

        return true;
    }

    private function validate_course_data($data) {
        foreach (self::REQUIRED_COURSE_FIELDS as $field) {
            if (empty($data[$field])) {
                throw new Exception("Missing required field: $field");
            }
        }

        if (!in_array($data['course_type'], self::ALLOWED_COURSE_TYPES)) {
            throw new Exception("Invalid course type: {$data['course_type']}");
        }

        if (!strtotime($data['course_date'])) {
            throw new Exception("Invalid date format: {$data['course_date']}");
        }

        // Validate participant counts
        if (!empty($data['participants_data'])) {
            $participants = is_array($data['participants_data']) 
                ? $data['participants_data'] 
                : json_decode($data['participants_data'], true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new Exception("Invalid participants data format");
            }

            foreach (['awareness', 'operations', 'technician'] as $level) {
                if (!isset($participants[$level]) || !is_numeric($participants[$level]) || $participants[$level] < 0) {
                    throw new Exception("Invalid {$level} count in participants data");
                }
            }
        }

        return true;
    }

    private function validate_assistant_data($assistants) {
        if (empty($assistants)) {
            return true;
        }

        if (count($assistants) > self::MAX_ASSISTANT_COUNT) {
            throw new Exception("Maximum number of assistants exceeded");
        }

        foreach ($assistants as $assistant) {
            if (empty($assistant['email']) || !is_email($assistant['email'])) {
                throw new Exception("Invalid assistant email: " . ($assistant['email'] ?? 'empty'));
            }

            if (empty($assistant['first_name']) || empty($assistant['last_name'])) {
                throw new Exception("Missing assistant name");
            }
        }

        return true;
    }
}