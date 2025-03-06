<?php
if (!defined('ABSPATH')) exit;

class LSIM_Instructor_Fields {
    public function __construct() {
        add_action('add_meta_boxes', [$this, 'add_meta_boxes']);
        add_action('save_post_instructor', [$this, 'save_instructor']);
        add_filter('manage_instructor_posts_columns', [$this, 'add_custom_columns']);
        add_action('manage_instructor_posts_custom_column', [$this, 'populate_custom_columns'], 10, 2);
        add_filter('manage_edit-instructor_sortable_columns', [$this, 'make_custom_columns_sortable']);
        add_action('admin_head', [$this, 'hide_title_field']);
        add_action('admin_enqueue_scripts', [$this, 'add_admin_scripts']);
        add_action('admin_footer-post.php', [$this, 'modify_submit_box']);
        add_action('admin_footer-post-new.php', [$this, 'modify_submit_box']);
    }

    public function hide_title_field() {
        global $post_type;
        if ($post_type === 'instructor') {
            echo '<style>#titlediv { display: none; }</style>';
        }
    }

    public function add_admin_scripts() {
        $screen = get_current_screen();
        if ($screen && $screen->post_type === 'instructor') {
            wp_enqueue_script('jquery');
            wp_enqueue_script(
                'lsim-admin-script',
                LSIM_PLUGIN_URL . 'assets/js/admin-scripts.js',
                ['jquery'],
                LSIM_VERSION,
                true
            );
            wp_localize_script('lsim-admin-script', 'lsim', [
                'nonce' => wp_create_nonce('save_instructor_nonce_action'),
                'ajaxurl' => admin_url('admin-ajax.php')
            ]);
        }
    }

    public function add_meta_boxes() {
        remove_meta_box('titlediv', 'instructor', 'normal');
        
        add_meta_box(
            'instructor_details',
            'Instructor Information & Certifications',
            [$this, 'render_combined_details'],
            'instructor',
            'normal',
            'high'
        );
        
        add_filter('get_user_option_meta-box-order_instructor', function($order) {
            return [
                'normal' => 'instructor_details',
                'side' => '',
                'advanced' => ''
            ];
        });
    }

    public function render_combined_details($post) {
        wp_nonce_field('save_instructor_nonce_action', 'instructor_details_nonce');
        $meta = get_post_meta($post->ID);
        ?>
        <style>
            .instructor-details-grid { 
                display: grid; 
                grid-template-columns: 1fr 1fr; 
                gap: 20px;
                margin-bottom: 20px;
            }
            .form-row { margin-bottom: 15px; }
            .form-row label { display: block; font-weight: bold; margin-bottom: 5px; }
            .form-row input[type="text"],
            .form-row input[type="email"],
            .form-row input[type="tel"] { width: 100%; }
            .required::after { content: " *"; color: #dc3232; }
            
            .certifications-grid {
                display: grid;
                grid-template-columns: 1fr 1fr;
                gap: 20px;
                margin-top: 20px;
                padding-top: 20px;
                border-top: 1px solid #ddd;
            }
            .certification-section {
                padding: 15px;
                border: 1px solid #ddd;
                border-radius: 4px;
            }
            .certification-section.active { background-color: #e7f6e7; }
            .certification-section h3 {
                margin: 0 0 15px 0;
                padding-bottom: 10px;
                border-bottom: 1px solid #ddd;
            }
            .certification-dates { margin-top: 10px; }
            .auth-date { margin-bottom: 10px; }
            .recert-date {
                margin: 5px 0;
                display: flex;
                align-items: center;
                gap: 10px;
            }
            .add-recert-date { margin-top: 10px; }
            .expiration-info {
                margin-top: 10px;
                padding: 5px;
                background: rgba(0,0,0,0.05);
                border-radius: 3px;
                font-size: 0.9em;
            }
            .spinner.active { visibility: visible; }
            .save-success {
                color: #008a20;
                margin-left: 10px;
                display: none;
            }
            .error-message {
                color: #dc3232;
                font-size: 12px;
                margin-top: 5px;
            }
            input.error {
                border-color: #dc3232;
            }
        </style>

        <div class="instructor-details-grid">
            <div>
                <div class="form-row">
                    <label class="required" for="first_name">First Name</label>
                    <input type="text" id="first_name" name="first_name" 
                           value="<?php echo esc_attr($meta['_first_name'][0] ?? ''); ?>" required>
                </div>

                <div class="form-row">
                    <label class="required" for="last_name">Last Name</label>
                    <input type="text" id="last_name" name="last_name" 
                           value="<?php echo esc_attr($meta['_last_name'][0] ?? ''); ?>" required>
                </div>

                <div class="form-row">
                    <label class="required" for="email">Email</label>
                    <input type="email" id="email" name="email" 
                           value="<?php echo esc_attr($meta['_email'][0] ?? ''); ?>" required>
                </div>
            </div>

            <div>
                <div class="form-row">
                    <label for="phone">Phone</label>
                    <input type="tel" id="phone" name="phone" 
                           value="<?php echo esc_attr($meta['_phone'][0] ?? ''); ?>">
                </div>

                <div class="form-row">
                    <label for="department">Department/Agency</label>
                    <input type="text" id="department" name="department" 
                           value="<?php echo esc_attr($meta['_department'][0] ?? ''); ?>">
                </div>

                <div class="form-row">
                    <label for="state">State</label>
                    <select id="state" name="state">
                        <option value="">Select State</option>
                        <?php
                        $states = array(
                            'AL'=>'Alabama', 'AK'=>'Alaska', 'AZ'=>'Arizona', 'AR'=>'Arkansas', 
                            'CA'=>'California', 'CO'=>'Colorado', 'CT'=>'Connecticut', 'DE'=>'Delaware', 
                            'DC'=>'District of Columbia', 'FL'=>'Florida', 'GA'=>'Georgia', 'HI'=>'Hawaii', 
                            'ID'=>'Idaho', 'IL'=>'Illinois', 'IN'=>'Indiana', 'IA'=>'Iowa', 
                            'KS'=>'Kansas', 'KY'=>'Kentucky', 'LA'=>'Louisiana', 'ME'=>'Maine', 
                            'MD'=>'Maryland', 'MA'=>'Massachusetts', 'MI'=>'Michigan', 'MN'=>'Minnesota', 
                            'MS'=>'Mississippi', 'MO'=>'Missouri', 'MT'=>'Montana', 'NE'=>'Nebraska', 
                            'NV'=>'Nevada', 'NH'=>'New Hampshire', 'NJ'=>'New Jersey', 'NM'=>'New Mexico', 
                            'NY'=>'New York', 'NC'=>'North Carolina', 'ND'=>'North Dakota', 'OH'=>'Ohio', 
                            'OK'=>'Oklahoma', 'OR'=>'Oregon', 'PA'=>'Pennsylvania', 'RI'=>'Rhode Island', 
                            'SC'=>'South Carolina', 'SD'=>'South Dakota', 'TN'=>'Tennessee', 'TX'=>'Texas', 
                            'UT'=>'Utah', 'VT'=>'Vermont', 'VA'=>'Virginia', 'WA'=>'Washington', 
                            'WV'=>'West Virginia', 'WI'=>'Wisconsin', 'WY'=>'Wyoming'
                        );
                        $current_state = $meta['_state'][0] ?? '';
                        foreach ($states as $code => $name) {
                            printf(
                                '<option value="%s" %s>%s</option>',
                                esc_attr($code),
                                selected($current_state, $code, false),
                                esc_html($name)
                            );
                        }
                        ?>
                    </select>
                </div>
            </div>
        </div>

        <?php
        $this->render_certifications($post, $meta);
    }
	private function render_certifications($post, $meta) {
        ?>
        <div class="certifications-grid">
            <?php $this->render_certification_section($post, 'ice', 'Ice Rescue', $meta); ?>
            <?php $this->render_certification_section($post, 'water', 'Water Rescue', $meta); ?>
        </div>
        <?php
    }

    private function render_certification_section($post, $type, $title, $meta) {
        $status_class = $this->get_certification_status_class($post->ID, $type);
        ?>
        <div class="certification-section <?php echo esc_attr($status_class); ?>">
            <h3><?php echo esc_html($title); ?> Certification</h3>
            <div class="certification-dates">
                <div class="auth-date">
                    <label>Original Authorization:</label>
                    <input type="date" name="<?php echo $type; ?>_original_date" 
                           value="<?php echo esc_attr($meta["_{$type}_original_date"][0] ?? ''); ?>"
                           class="certification-date">
                </div>

                <div id="<?php echo $type; ?>-recert-dates">
                    <?php
                    $recert_dates = get_post_meta($post->ID, "_{$type}_recert_dates", true) ?: [];
                    foreach ($recert_dates as $date) {
                        ?>
                        <div class="recert-date">
                            <input type="date" name="<?php echo $type; ?>_recert_dates[]" 
                                   value="<?php echo esc_attr($date); ?>">
                            <button type="button" class="button-link remove-date" 
                                    title="Remove date">&times;</button>
                        </div>
                        <?php
                    }
                    ?>
                </div>

                <div class="certification-actions">
                    <button type="button" class="button add-recert-date" 
                            data-type="<?php echo esc_attr($type); ?>">Add Recertification</button>
                </div>

                <?php if ($expiration = $this->get_certification_expiration($post->ID, $type)): ?>
                    <div class="expiration-info">
                        Expires: <?php echo date('F j, Y', strtotime($expiration)); ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        <?php
    }

    public function save_instructor($post_id) {
		if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
		if (!current_user_can('edit_post', $post_id)) return;
		if (!isset($_POST['instructor_details_nonce']) || 
			!wp_verify_nonce($_POST['instructor_details_nonce'], 'save_instructor_nonce_action')) {
			return;
		}

		// Add this new validation block here
		$course_date = isset($_POST['course_date']) ? $_POST['course_date'] : '';
		$course_type = isset($_POST['course_type']) ? $_POST['course_type'] : '';
		$course_location = isset($_POST['course_location']) ? $_POST['course_location'] : '';
		
		if (($course_date || $course_type || $course_location) && 
			(!$course_date || !$course_type || !$course_location)) {
			// Store error message
			add_settings_error(
				'lsim_messages',
				'incomplete_course',
				'Incomplete course data was not saved. Please fill all required course fields.',
				'error'
			);
			set_transient('lsim_admin_notices_' . get_current_user_id(), get_settings_errors(), 30);
			return;
		}

        // Save basic fields
        $fields = ['first_name', 'last_name', 'email', 'phone', 'department', 'state'];
        foreach ($fields as $field) {
            if (isset($_POST[$field])) {
                $value = $field === 'email' ? sanitize_email($_POST[$field]) : sanitize_text_field($_POST[$field]);
                update_post_meta($post_id, "_{$field}", $value);
            }
        }

        // Get the email that was just saved
        $email = sanitize_email($_POST['email']);

        // Check if email was changed
        $old_email = get_post_meta($post_id, '_email', true);
        if ($old_email !== $email) {
            global $wpdb;
            // Clear any pending records for the old email
            $wpdb->delete(
                $wpdb->prefix . 'lsim_pending_assistants',
                ['email' => $old_email],
                ['%s']
            );
            error_log(sprintf(
                'LSIM: Email changed for instructor %d from %s to %s',
                $post_id,
                $old_email,
                $email
            ));
        }
        
        // Handle pending assistant records
        if ($email) {
            global $wpdb;
            
            // Get all pending assistant records for this email
            $pending_records = $wpdb->get_results($wpdb->prepare(
                "SELECT * FROM {$wpdb->prefix}lsim_pending_assistants 
                WHERE email = %s",
                $email
            ));

            // Move pending records to confirmed assistant history
            foreach ($pending_records as $record) {
                $wpdb->insert(
                    $wpdb->prefix . 'lsim_assistant_history',
                    [
                        'instructor_id' => $post_id,
                        'course_id' => $record->course_id,
                        'created_at' => current_time('mysql')
                    ],
                    ['%d', '%d', '%s']
                );
            }

            // Delete the pending records
            if (!empty($pending_records)) {
                $wpdb->delete(
                    $wpdb->prefix . 'lsim_pending_assistants',
                    ['email' => $email],
                    ['%s']
                );

                error_log(sprintf(
                    'LSIM: Converted %d pending assistant records for instructor %d (email: %s)',
                    count($pending_records),
                    $post_id,
                    $email
                ));

                // Add notice about converted records
                add_action('admin_notices', function() use ($pending_records) {
                    ?>
                    <div class="notice notice-success is-dismissible">
                        <p>
                            <?php printf(
                                '%d pending assistant record(s) have been converted to confirmed records.',
                                count($pending_records)
                            ); ?>
                        </p>
                    </div>
                    <?php
                });
            }
        }

        $this->save_certification_data($post_id);
        $this->update_instructor_title($post_id);
    }

    public function modify_submit_box() {
        if (get_post_type() !== 'instructor') return;
        ?>
        <script>
        jQuery(document).ready(function($) {
            var nonce = '<?php echo wp_create_nonce('save_instructor_nonce_action'); ?>';
            $('#publish').val('Save Instructor');
            
            $('form#post').on('submit', function(e) {
                e.preventDefault();
                
                var required = ['first_name', 'last_name', 'email'];
                var isValid = true;
                
                required.forEach(function(field) {
                    var input = $('#' + field);
                    if (!input.val().trim()) {
                        isValid = false;
                        input.addClass('error');
                        if (!input.next('.error-message').length) {
                            input.after('<span class="error-message">This field is required</span>');
                        }
                    } else {
                        input.removeClass('error');
                        input.next('.error-message').remove();
                    }
                });
                
                if (!isValid) {
                    return false;
                }

                $('#publish').prop('disabled', true).val('Saving...');
                
                $.ajax({
                    url: ajaxurl,
                    type: 'POST',
                    data: {
                        action: 'save_instructor',
                        nonce: nonce,
                        form_data: $(this).serialize()
                    },
                    success: function(response) {
                        if (response.success) {
                            window.location.href = 'post.php?post=' + response.data.instructor_id + '&action=edit&message=1';
                        } else if (response.data.type === 'duplicate_email') {
                            handleDuplicateEmail(response);
                        } else {
                            alert(response.data.message || 'Error saving instructor');
                            $('#publish').prop('disabled', false).val('Save Instructor');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('AJAX Error:', {xhr: xhr, status: status, error: error});
                        alert('Error saving instructor: ' + error);
                        $('#publish').prop('disabled', false).val('Save Instructor');
                    }
                });
            });

            function handleDuplicateEmail(response) {
                var message = 'An instructor with this email already exists: ' + 
                            response.data.instructor.name + '\n\n' +
                            'Would you like to:\n' +
                            '1. Edit the existing instructor\n' +
                            '2. Use a different email\n' +
                            '3. Cancel';
                
                var choice = window.prompt(message, '1');
                
                if (choice === '1') {
                    window.location.href = response.data.instructor.edit_link;
                } else if (choice === '2') {
                    $('#email').focus().select();
                    $('#publish').prop('disabled', false).val('Save Instructor');
                } else {
                    $('#publish').prop('disabled', false).val('Save Instructor');
                }
            }
        });
        </script>
        <?php
    }

		private function save_certification_data($post_id) {
		$types = ['ice', 'water'];
		foreach ($types as $type) {
			lsim_log_debug("Saving {$type} certification data", [
				'post_id' => $post_id,
				'original_date' => $_POST["{$type}_original_date"] ?? null,
				'recert_dates' => $_POST["{$type}_recert_dates"] ?? []
			]);
			
			if (isset($_POST["{$type}_original_date"])) {
				update_post_meta($post_id, "_{$type}_original_date", 
					sanitize_text_field($_POST["{$type}_original_date"]));
			}

			if (isset($_POST["{$type}_recert_dates"])) {
				$dates = array_map('sanitize_text_field', $_POST["{$type}_recert_dates"]);
				$dates = array_filter($dates);
				sort($dates);
				update_post_meta($post_id, "_{$type}_recert_dates", $dates);
			}
		}
	}

    private function update_instructor_title($post_id) {
        if (!empty($_POST['first_name']) && !empty($_POST['last_name'])) {
            $title = sanitize_text_field($_POST['last_name'] . ', ' . $_POST['first_name']);
            remove_action('save_post_instructor', [$this, 'save_instructor']);
            wp_update_post(['ID' => $post_id, 'post_title' => $title]);
            add_action('save_post_instructor', [$this, 'save_instructor']);
        }
    }

    private function get_certification_status_class($post_id, $type) {
        $expiration = $this->get_certification_expiration($post_id, $type);
        if (!$expiration) return 'inactive';
        return (strtotime($expiration) >= current_time('timestamp')) ? 'active' : 'expired';
    }

    private function get_certification_expiration($post_id, $type) {
        $dates = [];
        
        $original_date = get_post_meta($post_id, "_{$type}_original_date", true);
        if ($original_date) {
            $dates[] = $original_date;
        }
        
        $recert_dates = get_post_meta($post_id, "_{$type}_recert_dates", true) ?: [];
        $dates = array_merge($dates, $recert_dates);
        
        if (empty($dates)) {
            return null;
        }
        
        $clean_dates = array_filter($dates);
        if (empty($clean_dates)) {
            return null;
        }
        
        sort($clean_dates);
        $most_recent = end($clean_dates);
        $cert_year = date('Y', strtotime($most_recent));
        
        return date('Y-m-d', strtotime("$cert_year-12-31 +3 years"));
    }

    public function add_custom_columns($columns) {
        $new_columns = [];
        foreach ($columns as $key => $value) {
            if ($key === 'title') {
                $new_columns[$key] = 'Name';
                $new_columns['department'] = 'Department/Agency';
                $new_columns['state'] = 'State';
                $new_columns['ice_rescue'] = 'Ice Rescue Status';
                $new_columns['water_rescue'] = 'Water Rescue Status';
            } else {
                $new_columns[$key] = $value;
            }
        }
        return $new_columns;
    }

    public function populate_custom_columns($column, $post_id) {
        switch ($column) {
            case 'department':
                echo esc_html(get_post_meta($post_id, '_department', true));
                break;
            case 'state':
                echo esc_html(get_post_meta($post_id, '_state', true));
                break;
            case 'ice_rescue':
                $this->display_certification_status($post_id, 'ice');
                break;
            case 'water_rescue':
                $this->display_certification_status($post_id, 'water');
                break;
        }
    }

    private function display_certification_status($post_id, $type) {
        if ($expiration = $this->get_certification_expiration($post_id, $type)) {
            $is_active = strtotime($expiration) >= current_time('timestamp');
            $status_class = $is_active ? 'status-active' : 'status-expired';
            $status_text = $is_active ? 'Active' : 'Expired';
            printf(
                '<span class="%s">%s<br><small>Expires: %s</small></span>',
                esc_attr($status_class),
                esc_html($status_text),
                esc_html(date('M j, Y', strtotime($expiration)))
            );
        } else {
            echo '<span class="status-none">Not Certified</span>';
        }
    }

    public function make_custom_columns_sortable($columns) {
        $columns['department'] = 'department';
        $columns['state'] = 'state';
        return $columns;
    }
}