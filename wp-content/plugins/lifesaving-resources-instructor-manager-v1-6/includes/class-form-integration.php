<?php
if (!defined('ABSPATH')) exit;

class LSIM_Form_Integration {
    private function get_field_mappings($type) {
        $mappings = get_option('lsim_form_field_mappings', []);
        
        $default_mappings = [
            'ice' => [
                'first_name' => '3.3',
                'last_name' => '3.6',
                'department' => '5',
                'address' => '6',
                'phone' => '7',
                'email' => '8',
                'course_date' => '12',
                'location' => '13',
                'participants_awareness' => '15',    // IRA
                'participants_technician' => '16',   // IRT
                'participants_operations' => '17',   // IRO
                'hours' => '40',
                'assistant_list' => '28'            // New single field for assistants list
            ],
            'water' => [
                'first_name' => '3.3',
                'last_name' => '3.6',
                'department' => '5',
                'address' => '6',
                'phone' => '7',
                'email' => '8',
                'course_date' => '12',
                'location' => '13',
                'participants_awareness' => '15',    // WRA
                'participants_technician' => '16',   // WRT
                'participants_operations' => '17',   // WRO
                'participants_surf_swiftwater' => '18', // WRS
                'hours' => '39',
                'assistant_list' => '28'            // New single field for assistants list
            ]
        ];
        
        return $mappings[$type] ?? $default_mappings[$type];
    }

    public function __construct() {
        add_action('gform_after_submission_' . $this->get_form_id('ice'), [$this, 'process_ice_course_submission'], 10, 2);
        add_action('gform_after_submission_' . $this->get_form_id('water'), [$this, 'process_water_course_submission'], 10, 2);
        add_action('admin_notices', [$this, 'show_unrecognized_instructor_notice']);
    }

    private function get_form_id($type) {
        $form_ids = get_option('lsim_form_ids', [
            'ice' => 3,
            'water' => 1
        ]);
        return $form_ids[$type] ?? ($type === 'ice' ? 3 : 1);
    }

    public function process_ice_course_submission($entry, $form) {
        $this->process_course_submission($entry, 'ice');
    }

    public function process_water_course_submission($entry, $form) {
        $this->process_course_submission($entry, 'water');
    }

    private function process_course_submission($entry, $type) {
        $fields = $this->get_field_mappings($type);
        
        // Add debugging
        error_log('LSIM Debug - Form Entry Data: ' . print_r([
            'entry' => $entry,
            'type' => $type,
            'first_name_field' => $fields['first_name'],
            'last_name_field' => $fields['last_name'],
            'first_name_value' => rgar($entry, $fields['first_name']),
            'last_name_value' => rgar($entry, $fields['last_name'])
        ], true));

        // Get instructor email from form
        $instructor_email = rgar($entry, $fields['email']);
        $instructor = $this->get_instructor_by_email($instructor_email);

        if (!$instructor) {
            $this->store_unrecognized_submission($entry, $type);
            $this->send_unrecognized_instructor_notification($instructor_email, $entry, $type);
            return;
        }

        // Prepare participant data
        $participants = $this->prepare_participant_data($entry, $type, $fields);

		// Process assistant instructors using the list field - add error handling
		if (!empty($fields['assistant_list'])) {
			$assistants = $this->process_assistant_data($entry, $fields['assistant_list']);
		} else {
			$assistants = [];
			error_log('LSIM Debug - No assistant list field found in mappings');
		}

        // Add course record
        $course_id = $this->add_course_to_instructor($instructor->ID, [
            'type' => $type,
            'form_id' => $entry['form_id'],
            'entry_id' => $entry['id'],
            'date' => rgar($entry, $fields['course_date']),
            'location' => rgar($entry, $fields['location']),
            'hours' => rgar($entry, $fields['hours']),
            'participants' => $participants
        ]);

        // Process assistants for this course
        if ($course_id) {
            $this->process_course_assistants($course_id, $assistants);
        }
    }
private function prepare_participant_data($entry, $type, $fields) {
        if ($type === 'ice') {
            return [
                'awareness' => intval(rgar($entry, $fields['participants_awareness'])),
                'technician' => intval(rgar($entry, $fields['participants_technician'])),
                'operations' => intval(rgar($entry, $fields['participants_operations'])),
                'surf_swiftwater' => 0
            ];
        } else {
            return [
                'awareness' => intval(rgar($entry, $fields['participants_awareness'])),
                'technician' => intval(rgar($entry, $fields['participants_technician'])),
                'operations' => intval(rgar($entry, $fields['participants_operations'])),
                'surf_swiftwater' => intval(rgar($entry, $fields['participants_surf_swiftwater']))
            ];
        }
    }

	private function process_assistant_data($entry, $list_field_id) {
		$assistants = [];
		
		// Get the raw list data
		$list_data = rgar($entry, $list_field_id);
		error_log('LSIM Debug - Raw Assistant List Data: ' . print_r($list_data, true));

		// If we have data, unserialize it
		if (!empty($list_data)) {
			$unserialized_data = maybe_unserialize($list_data);
			error_log('LSIM Debug - Unserialized Assistant Data: ' . print_r($unserialized_data, true));

			if (is_array($unserialized_data)) {
				foreach ($unserialized_data as $assistant) {
					if (!empty($assistant['First Name']) && 
						!empty($assistant['Last Name']) && 
						!empty($assistant['Email'])) {
						$assistants[] = [
							'first_name' => $assistant['First Name'],
							'last_name' => $assistant['Last Name'],
							'email' => $assistant['Email']
						];
					}
				}
			}
		}
		
		error_log('LSIM Debug - Processed Assistant Data: ' . print_r($assistants, true));
		return $assistants;
	}

    private function process_course_assistants($course_id, $assistants) {
        global $wpdb;

        foreach ($assistants as $assistant_data) {
            error_log('Processing assistant data: ' . print_r($assistant_data, true));

            $instructor = $this->get_instructor_by_email($assistant_data['email']);
            
            if ($instructor) {
                // Record registered assistant
                $result = $wpdb->insert(
                    $wpdb->prefix . 'lsim_assistant_history',
                    [
                        'instructor_id' => $instructor->ID,
                        'course_id' => $course_id,
                        'created_at' => current_time('mysql')
                    ],
                    ['%d', '%d', '%s']
                );

                if ($result === false) {
                    error_log('DB Insert Error (process_course_assistants - registered): ' . $wpdb->last_error);
                } else {
                    error_log('DB Insert Success (process_course_assistants - registered): ' . $wpdb->insert_id);
                }
            } else {
                // Record pending assistant
                $result = $wpdb->insert(
                    $wpdb->prefix . 'lsim_pending_assistants',
                    [
                        'course_id' => $course_id,
                        'first_name' => $assistant_data['first_name'],
                        'last_name' => $assistant_data['last_name'],
                        'email' => $assistant_data['email'],
                        'created_at' => current_time('mysql')
                    ],
                    ['%d', '%s', '%s', '%s', '%s']
                );

                if ($result === false) {
                    error_log('DB Insert Error (process_course_assistants - pending): ' . $wpdb->last_error);
                } else {
                    error_log('DB Insert Success (process_course_assistants - pending): ' . $wpdb->insert_id);
                }
            }
        }
    }

    private function add_course_to_instructor($instructor_id, $course_data) {
        global $wpdb;

        error_log('Attempting to insert course data: ' . print_r($course_data, true));

        $result = $wpdb->insert(
            $wpdb->prefix . 'lsim_course_history',
            [
                'instructor_id' => $instructor_id,
                'course_type' => $course_data['type'],
                'course_date' => $course_data['date'],
                'location' => $course_data['location'],
                'participants_data' => json_encode($course_data['participants']),
                'form_entry_id' => $course_data['entry_id'],
                'created_at' => current_time('mysql')
            ],
            ['%d', '%s', '%s', '%s', '%s', '%d', '%s']
        );

        if ($result === false) {
            error_log('DB Insert Error (add_course_to_instructor): ' . $wpdb->last_error);
        } else {
            error_log('DB Insert Success (add_course_to_instructor): ' . $wpdb->insert_id);
        }

        if ($result) {
            $course_id = $wpdb->insert_id;

            // Update the certification type taxonomy
            $term = get_term_by('name', ucfirst($course_data['type']) . ' Rescue', 'certification_type');
            if ($term) {
                wp_set_object_terms($instructor_id, $term->term_id, 'certification_type', true);
            }

            return $course_id;
        }

        return false;
    }

    private function store_unrecognized_submission($entry, $type) {
        $fields = $this->get_field_mappings($type);
        
        // Get first and last name and combine them
        $first_name = trim(rgar($entry, $fields['first_name']));
        $last_name = trim(rgar($entry, $fields['last_name']));
        $name = sprintf('%s %s', $first_name, $last_name);
        
        $unrecognized = get_option('lsim_unrecognized_submissions', []);
        $unrecognized[] = [
            'entry_id' => $entry['id'],
            'form_id' => $entry['form_id'],
            'type' => $type,
            'email' => rgar($entry, $fields['email']),
            'name' => $name,
            'course_date' => rgar($entry, $fields['course_date']),
            'location' => rgar($entry, $fields['location']),
            'date_submitted' => current_time('mysql')
        ];
        update_option('lsim_unrecognized_submissions', $unrecognized);
    }

    private function send_unrecognized_instructor_notification($email, $entry, $type) {
		$fields = $this->get_field_mappings($type);
		
		// Add debug logging right after we get the fields
		error_log('LSIM Debug - Fields in notification: ' . print_r($fields, true));
		error_log('LSIM Debug - Entry in notification: ' . print_r($entry, true));
		
		$admin_email = get_option('admin_email');
		$subject = 'Unrecognized Instructor Submission - Action Required';
        
        // Combine first and last name
        $first_name = rgar($entry, $fields['first_name']);
        $last_name = rgar($entry, $fields['last_name']);
        $full_name = trim("$first_name $last_name");
        
        $message = sprintf(
            "A course completion form was submitted by an unrecognized instructor:\n\n" .
            "Email: %s\n" .
            "Name: %s\n" .
            "Course Type: %s Rescue\n" .
            "Date: %s\n" .
            "Location: %s\n\n" .
            "Assistants:\n",
            $email,
            $full_name,
            ucfirst($type),
            rgar($entry, $fields['course_date']),
            rgar($entry, $fields['location'])
        );

        // Add assistant information using the list field - with error handling
		if (!empty($fields['assistant_list'])) {
			$list_data = rgar($entry, $fields['assistant_list']);
			if (!empty($list_data) && is_array($list_data)) {
				for ($i = 0; $i < count($list_data); $i += 3) {
					$fname = trim($list_data[$i] ?? '');
					$lname = trim($list_data[$i + 1] ?? '');
					$email = trim($list_data[$i + 2] ?? '');

					if ($fname && $lname && $email) {
						$message .= sprintf("- %s %s (%s)\n", $fname, $lname, $email);
					}
				}
			}
		} else {
			error_log('LSIM Debug - No assistant list field found in mappings for notification');
		}

        $message .= sprintf(
            "\nPlease review this submission and either:\n" .
            "1. Create a new instructor record with this email\n" .
            "2. Update the existing instructor's email\n\n" .
            "View the form entry here: %s",
            admin_url(sprintf(
                'admin.php?page=gf_entries&view=entry&id=%d&lid=%d',
                $entry['form_id'],
                $entry['id']
            ))
        );

        wp_mail($admin_email, $subject, $message);
    }

    public function show_unrecognized_instructor_notice() {
        $screen = get_current_screen();
        if (!$screen || $screen->post_type !== 'instructor') {
            return;
        }

        $unrecognized = get_option('lsim_unrecognized_submissions', []);
        if (!empty($unrecognized)) {
            ?>
            <div class="notice notice-warning is-dismissible">
                <p>
                    There are <?php echo count($unrecognized); ?> course completion submissions from unrecognized instructors.
                    <a href="<?php echo admin_url('admin.php?page=instructor-settings&tab=unrecognized'); ?>">
                        Review submissions
                    </a>
                </p>
            </div>
            <?php
        }
    }

    private function get_instructor_by_email($email) {
        $instructors = get_posts([
            'post_type' => 'instructor',
            'meta_key' => '_email',
            'meta_value' => $email,
            'posts_per_page' => 1
        ]);
        return !empty($instructors) ? $instructors[0] : null;
    }
}	