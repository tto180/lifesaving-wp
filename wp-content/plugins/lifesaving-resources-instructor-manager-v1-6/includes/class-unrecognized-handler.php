<?php
if (!defined('ABSPATH')) exit;

class LSIM_Unrecognized_Handler {
    public function __construct() {
        add_action('lifesaving_resources_dashboard', [$this, 'render_dashboard_widget']);
        add_action('wp_ajax_process_unrecognized', [$this, 'process_unrecognized_submission']);
        add_action('wp_ajax_dismiss_unrecognized', [$this, 'dismiss_unrecognized_submission']);
    }

    private function get_field_mappings($type) {
        $mappings = get_option('lsim_form_field_mappings', []);
        
        $default_mappings = [
            'ice' => [
                'first_name' => '4.3',
                'last_name' => '4.6',
                'department' => '5',
                'address' => '6',
                'phone' => '7',
                'email' => '8',
                'course_date' => '12',
                'location' => '13',
                'hours' => '40',
                'participants_awareness' => '15',
                'participants_technician' => '16',
                'participants_operations' => '17',
                'assistant_list' => '28'
            ],
            'water' => [
                'first_name' => '4.3',
                'last_name' => '4.6',
                'department' => '5',
                'address' => '6',
                'phone' => '7',
                'email' => '8',
                'course_date' => '12',
                'location' => '13',
                'hours' => '39',
                'participants_awareness' => '15',
                'participants_technician' => '16',
                'participants_operations' => '17',
                'participants_surf_swiftwater' => '18',
                'assistant_list' => '28'
            ]
        ];
        
        return isset($mappings[$type]) ? $mappings[$type] : $default_mappings[$type];
    }

    private function process_assistant_data($entry, $field_id) {
        $assistants = [];
        
        if (empty($field_id) || empty($entry)) {
            return $assistants;
        }
        
        // Get the raw list data
        $raw_data = rgar($entry, $field_id);
        
        lsim_log_debug('Processing assistant data from field', [
            'field_id' => $field_id,
            'raw_data' => $raw_data
        ]);
        
        if (empty($raw_data)) {
            return $assistants;
        }
        
        // Unserialize the data
        $list_data = maybe_unserialize($raw_data);
        
        if (!is_array($list_data)) {
            lsim_log_debug('Invalid assistant data format', [
                'data' => $list_data
            ]);
            return $assistants;
        }
        
        // Process each assistant entry
        foreach ($list_data as $assistant) {
            if (empty($assistant) || !is_array($assistant)) {
                continue;
            }
            
            // Check for both possible key formats
            $first_name = $assistant['First Name'] ?? $assistant['first_name'] ?? '';
            $last_name = $assistant['Last Name'] ?? $assistant['last_name'] ?? '';
            $email = $assistant['Email'] ?? $assistant['email'] ?? '';
            
            if (!empty($first_name) && !empty($last_name) && !empty($email)) {
                $assistants[] = [
                    'first_name' => sanitize_text_field($first_name),
                    'last_name' => sanitize_text_field($last_name),
                    'email' => sanitize_email($email)
                ];
            }
        }
        
        lsim_log_debug('Processed assistant data', [
            'assistants' => $assistants
        ]);
        
        return $assistants;
    }

	private function store_unrecognized_submission($entry, $type) {
		if (empty($entry) || empty($type)) {
			lsim_log_error('Invalid data for unrecognized submission', [
				'entry' => $entry,
				'type' => $type
			]);
			return false;
		}

		$fields = $this->get_field_mappings($type);
		
		// Get the values directly from the form entry using the correct field IDs
		$first_name = trim(rgar($entry, '4.3'));
		$last_name = trim(rgar($entry, '4.6'));
		
		lsim_log_debug('Form field values:', [
			'first_name_field' => '4.3',
			'last_name_field' => '4.6',
			'first_name_value' => $first_name,
			'last_name_value' => $last_name,
			'raw_entry' => $entry
		]);

		$unrecognized = get_option('lsim_unrecognized_submissions', []);
		$submission_data = [
			'entry_id' => $entry['id'],
			'form_id' => $entry['form_id'],
			'type' => $type,
			'email' => sanitize_email(rgar($entry, $fields['email'])),
			'first_name' => $first_name,  // Store directly from form field
			'last_name' => $last_name,    // Store directly from form field
			'department' => sanitize_text_field(rgar($entry, $fields['department'])),
			'phone' => sanitize_text_field(rgar($entry, $fields['phone'])),
			'course_date' => sanitize_text_field(rgar($entry, $fields['course_date'])),
			'location' => sanitize_text_field(rgar($entry, $fields['location'])),
			'date_submitted' => current_time('mysql')
		];

		lsim_log_debug('Storing submission data:', $submission_data);
		
		$unrecognized[] = $submission_data;
		return update_option('lsim_unrecognized_submissions', $unrecognized);
	}
	
//part 2

public function render_dashboard_widget() {
        $submissions = get_option('lsim_unrecognized_submissions', []);
        
        // Add nonce fields to be used by JavaScript
        $dismiss_nonce = wp_create_nonce('dismiss_unrecognized_nonce');
        $process_nonce = wp_create_nonce('process_unrecognized_nonce');
        ?>
        <div class="postbox unrecognized-submissions-box">
            <h2 class="hndle"><span>Unrecognized Course Submissions</span></h2>
            <div class="inside unrecognized-submissions-widget">
                <input type="hidden" id="dismiss_unrecognized_nonce" value="<?php echo esc_attr($dismiss_nonce); ?>">
                <input type="hidden" id="process_unrecognized_nonce" value="<?php echo esc_attr($process_nonce); ?>">
                
                <?php if (empty($submissions)): ?>
						<p>No unrecognized submissions found.</p>
					<?php else: ?>
						<?php lsim_log_debug('Current submissions:', $submissions); ?>
						<script>
						console.log('Submissions data:', <?php echo json_encode($submissions); ?>);
						</script>
						<div class="table-responsive">
							<table class="widefat striped">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Email</th>
                                    <th>Course Type</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($submissions as $index => $submission): ?>
                                    <tr>
                                        <td><?php echo esc_html(date('M j, Y', strtotime($submission['date_submitted']))); ?></td>
                                        <td>
                                            <?php echo esc_html($submission['email']); ?>
                                            <div class="row-actions">
                                                <?php if (!empty($submission['first_name']) && !empty($submission['last_name'])): ?>
                                                    Name: <?php echo esc_html($submission['first_name'] . ' ' . $submission['last_name']); ?><br>
                                                <?php endif; ?>
                                                Course Date: <?php echo esc_html($submission['course_date']); ?><br>
                                                Location: <?php echo esc_html($submission['location']); ?>
                                                <?php if (!empty($submission['assistants'])): ?>
                                                    <br>Assistants: <?php 
                                                    $assistant_names = array_map(function($assistant) {
                                                        return sprintf('%s %s', 
                                                            esc_html($assistant['first_name']),
                                                            esc_html($assistant['last_name'])
                                                        );
                                                    }, $submission['assistants']);
                                                    echo implode(', ', $assistant_names);
                                                    ?>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                        <td><?php echo esc_html(ucfirst($submission['type'])) . ' Rescue'; ?></td>
                                        <td>
                                            <button class="button button-small create-instructor" 
                                                    data-submission="<?php echo esc_attr($index); ?>"
                                                    data-email="<?php echo esc_attr($submission['email']); ?>">
                                                Create Instructor
                                            </button>
                                            <button class="button button-small dismiss-submission" 
                                                    data-submission="<?php echo esc_attr($index); ?>">
                                                Dismiss
                                            </button>
                                            <a href="<?php echo admin_url(sprintf(
                                                'admin.php?page=gf_entries&view=entry&id=%d&lid=%d',
                                                $submission['form_id'],
                                                $submission['entry_id']
                                            )); ?>" class="button button-small" target="_blank">
                                                View Form
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <script>
        jQuery(document).ready(function($) {
            $('.create-instructor').on('click', function() {
                const $button = $(this);
                const submissionId = $button.data('submission');
                
                $button.prop('disabled', true).text('Processing...');
                
                $.ajax({
                    url: ajaxurl,
                    type: 'POST',
                    data: {
                        action: 'process_unrecognized',
                        nonce: $('#process_unrecognized_nonce').val(),
                        submission_id: submissionId
                    },
                    success: function(response) {
                        if (response.success && response.data && response.data.redirect) {
                            window.location.href = response.data.redirect;
                        } else {
                            let errorMessage = 'Error processing submission';
                            if (response.data && response.data.message) {
                                errorMessage = response.data.message;
                            }
                            console.error('Process Error:', response);
                            alert(errorMessage);
                            $button.prop('disabled', false).text('Create Instructor');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('AJAX Error:', {
                            xhr: xhr,
                            status: status,
                            error: error,
                            response: xhr.responseText
                        });
                        alert('Server error occurred. Please try again.');
                        $button.prop('disabled', false).text('Create Instructor');
                    }
                });
            });

            $('.dismiss-submission').on('click', function() {
                const $button = $(this);
                const $row = $button.closest('tr');
                const submissionId = $button.data('submission');
                
                if (!confirm('Are you sure you want to dismiss this submission?')) {
                    return;
                }
                
                $button.prop('disabled', true).text('Dismissing...');
                
                $.ajax({
                    url: ajaxurl,
                    type: 'POST',
                    data: {
                        action: 'dismiss_unrecognized',
                        nonce: $('#dismiss_unrecognized_nonce').val(),
                        submission_id: submissionId
                    },
                    success: function(response) {
                        if (response.success) {
                            $row.fadeOut(400, function() {
                                $(this).remove();
                                if ($('tbody tr').length === 0) {
                                    location.reload();
                                }
                            });
                        } else {
                            console.error('Dismiss Error:', response);
                            $button.prop('disabled', false).text('Dismiss');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('AJAX Error:', {
                            xhr: xhr,
                            status: status,
                            error: error,
                            response: xhr.responseText
                        });
                        $button.prop('disabled', false).text('Dismiss');
                        if (xhr.responseText && xhr.responseText.includes('success')) {
                            $row.fadeOut(400, function() {
                                $(this).remove();
                                if ($('tbody tr').length === 0) {
                                    location.reload();
                                }
                            });
                        } else {
                            alert('Server error occurred. Please try again.');
                        }
                    }
                });
            });

            $(window).on('error', function(e) {
                console.error('JavaScript Error:', e);
            });

            $(document).ajaxError(function(event, jqXHR, settings, error) {
                console.error('Global AJAX Error:', {
                    event: event,
                    jqXHR: jqXHR,
                    settings: settings,
                    error: error
                });
            });
        });
        </script>
        <?php
    }

	public function dismiss_unrecognized_submission() {
		lsim_log_debug('Dismiss submission request data:', [
			'post' => $_POST,
			'submissions' => get_option('lsim_unrecognized_submissions', [])
		]);
		
		check_ajax_referer('dismiss_unrecognized_nonce', 'nonce');
		
		if (!current_user_can('manage_options')) {
			wp_send_json_error(['message' => 'Unauthorized access']);
			return;
		}
			
			$submission_id = intval($_POST['submission_id']);
			$submissions = get_option('lsim_unrecognized_submissions', []);
			
			if (!isset($submissions[$submission_id])) {
				wp_send_json_error(['message' => 'Submission not found']);
				return;
			}
			
			lsim_log_debug('Dismissing unrecognized submission', [
				'submission_id' => $submission_id,
				'submission' => $submissions[$submission_id]
			]);
			
			unset($submissions[$submission_id]);
			$updated = update_option('lsim_unrecognized_submissions', array_values($submissions));
			
			if ($updated) {
				wp_send_json_success(['message' => 'Submission dismissed successfully']);
			} else {
				wp_send_json_error(['message' => 'Failed to update submissions']);
			}
		}

	public function process_unrecognized_submission() {
		lsim_log_debug('Process submission request data:', [
			'post' => $_POST,
			'submissions' => get_option('lsim_unrecognized_submissions', [])
		]);
		
		check_ajax_referer('process_unrecognized_nonce', 'nonce');
		
		if (!current_user_can('manage_options')) {
			wp_send_json_error(['message' => 'Unauthorized access']);
			return;
		}
		
		$submission_id = intval($_POST['submission_id']);
		$submissions = get_option('lsim_unrecognized_submissions', []);
		
		if (!isset($submissions[$submission_id])) {
			wp_send_json_error(['message' => 'Submission not found']);
			return;
		}
		
		$submission = $submissions[$submission_id];
		
			try {
			$data_manager = LSIM_Data_Manager::get_instance();
			
			// Create base instructor data
			$instructor_data = [
				'first_name' => $submission['first_name'],
				'last_name' => $submission['last_name'],
				'email' => $submission['email'],
				'phone' => $submission['phone'] ?? '',
				'department' => $submission['department'] ?? '',
				'state' => ''
			];

			lsim_log_debug('Creating instructor with data', [
				'instructor_data' => $instructor_data,
				'full_submission' => $submission
			]);
			
			$post_id = $data_manager->save_instructor($instructor_data);
			
			if ($post_id) {
				// Save all course-related metadata
				$meta_fields = [
					'_last_course_date' => $submission['course_date'],
					'_last_course_location' => $submission['location'],
					'_last_course_type' => $submission['type'],
					'_last_course_hours' => $submission['hours']
				];

				foreach ($meta_fields as $key => $value) {
					if (!empty($value)) {
						update_post_meta($post_id, $key, $value);
					}
				}
				
				// Process assistants if present
				if (!empty($submission['assistants'])) {
					foreach ($submission['assistants'] as $assistant) {
						if (!empty($assistant['email'])) {
							global $wpdb;
							$wpdb->insert(
								$wpdb->prefix . 'lsim_pending_assistants',
								[
									'course_id' => $post_id,
									'first_name' => $assistant['first_name'],
									'last_name' => $assistant['last_name'],
									'email' => $assistant['email'],
									'created_at' => current_time('mysql')
								],
								['%s', '%s', '%s', '%s']
							);
						}
					}
				}

				// Clean up and redirect
				unset($submissions[$submission_id]);
				update_option('lsim_unrecognized_submissions', array_values($submissions));
				
				wp_send_json_success([
					'redirect' => admin_url("post.php?post={$post_id}&action=edit")
				]);
			} else {
				throw new Exception($data_manager->get_last_error() ?: 'Unknown error occurred');
			}
			
		} catch (Exception $e) {
			lsim_log_error('Error processing submission', [
				'error' => $e->getMessage(),
				'submission' => $submission
			]);
			
			wp_send_json_error([
				'message' => $e->getMessage(),
				'data' => $submission
			]);
		}
	}
}	