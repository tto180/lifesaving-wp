<?php
if (!defined('ABSPATH')) exit;

class LSIM_Instructor_History {
    public function __construct() {
        add_action('add_meta_boxes', [$this, 'add_history_meta_boxes']);
        add_action('wp_ajax_save_course', [$this, 'handle_save_course']);
        add_action('wp_ajax_get_course', [$this, 'handle_get_course']);
        add_action('wp_ajax_delete_course', [$this, 'handle_delete_course']);
    }

    public function add_history_meta_boxes() {
        add_meta_box(
            'instructor_course_history',
            'Course History',
            [$this, 'render_course_history'],
            'instructor',
            'normal',
            'core'
        );
    }

    public function render_course_history($post) {
        error_log('Debug post info: ' . print_r([
            'ID' => $post->ID,
            'status' => $post->post_status,
            'is_new' => isset($_GET['post_type']),
            'URL' => $_SERVER['REQUEST_URI']
        ], true));
        
        // Check if this is a new instructor
        $is_new = !$post->ID || $post->post_status === 'auto-draft' || isset($_GET['post_type']);
        
        if ($is_new) {
            ?>
            <div class="notice notice-info">
                <p>Please save the instructor information first to add course history.</p>
                <style>
                    .notice {
                        background: #f0f6fc;
                        border-left: 4px solid #72aee6;
                        padding: 12px;
                        margin: 15px 0;
                    }
                    .notice p {
                        margin: 0;
                        padding: 0;
                    }
                </style>
            </div>
            <?php
            return;
        }

        // Only show course history for existing instructors
        global $wpdb;
        $courses = $wpdb->get_results($wpdb->prepare(
            "SELECT * FROM {$wpdb->prefix}lsim_course_history 
            WHERE instructor_id = %d 
            ORDER BY course_date DESC",
            $post->ID
        ));
        ?>
        
        <div class="course-history-wrapper">
            <div class="add-course-form">
                <?php wp_nonce_field(LSIM_NONCE_ACTION, 'course_nonce'); ?>
                <h3>Add Course</h3>
                <div class="course-form-grid">
                    <div class="form-row">
                        <label for="course_date" class="required">Date</label>
                        <input type="date" id="course_date" name="course_date" required>
                    </div>
                    <div class="form-row">
                        <label for="course_type" class="required">Type</label>
                        <select id="course_type" name="course_type" required>
                            <option value="">Select Type</option>
                            <option value="water">Water Rescue</option>
                            <option value="ice">Ice Rescue</option>
                        </select>
                    </div>
                    <div class="form-row">
                        <label for="course_location" class="required">Location</label>
                        <input type="text" id="course_location" name="course_location" required>
                    </div>
                </div>
                
                <div class="assistants-section">
                    <h4>Assistant Instructors</h4>
                    <div id="assistant-list"></div>
                    <button type="button" class="button add-assistant">Add Assistant</button>
                </div>

                <div class="certification-counts">
                    <h4>Student Certification Counts</h4>
                    <div class="counts-grid">
                        <div class="form-row">
                            <label for="count_awareness">Awareness</label>
                            <input type="number" id="count_awareness" name="count_awareness" min="0" value="0">
                        </div>
                        <div class="form-row">
                            <label for="count_operations">Operations</label>
                            <input type="number" id="count_operations" name="count_operations" min="0" value="0">
                        </div>
                        <div class="form-row">
                            <label for="count_technician">Technician</label>
                            <input type="number" id="count_technician" name="count_technician" min="0" value="0">
                        </div>
                        <div class="form-row surf-swiftwater-count" style="display: none;">
                            <label for="count_surf_swiftwater">Surf/Swiftwater</label>
                            <input type="number" id="count_surf_swiftwater" name="count_surf_swiftwater" min="0" value="0">
                        </div>
                    </div>
                </div>

                <div class="form-actions">
                    <input type="hidden" id="course_id" value="">
                    <button type="button" class="button button-primary" id="save-course">Save Course & Update Instructor</button>
                    <button type="button" class="button" id="cancel-edit" style="display: none;">Cancel</button>
                </div>
            </div>

		<h3>Course History</h3>
					<table class="widefat striped">
						<thead>
							<tr>
								<th>Date</th>
								<th>Type</th>
								<th>Location</th>
								<th>Assistants</th>
								<th>Certification Counts</th>
								<th>Actions</th>
							</tr>
						</thead>
						<tbody>
							<?php if (empty($courses)): ?>
								<tr><td colspan="6">No courses recorded yet.</td></tr>
							<?php else: ?>
								<?php foreach ($courses as $course): ?>
									<tr>
										<td><?php echo esc_html(date('M j, Y', strtotime($course->course_date))); ?></td>
										<td><?php echo esc_html(ucfirst($course->course_type)); ?></td>
										<td><?php echo esc_html($course->location); ?></td>
										<td>
											<?php 
											$assistants = $this->get_course_assistants($course->id);
											foreach ($assistants as $assistant) {
												echo '<div class="assistant-entry">';
												echo esc_html($assistant->first_name . ' ' . $assistant->last_name);
												if (!empty($assistant->email)) {
													echo ' (' . esc_html($assistant->email) . ')';
												}
												if (isset($assistant->pending) && $assistant->pending) {
													echo ' <span class="pending-badge">Pending</span>';
												}
												echo '</div>';
											}
											?>
										</td>
										<td>
											<?php 
											$counts = json_decode($course->participants_data, true) ?: [];
											$awareness = isset($counts['awareness']) ? $counts['awareness'] : 0;
											$operations = isset($counts['operations']) ? $counts['operations'] : 0;
											$technician = isset($counts['technician']) ? $counts['technician'] : 0;

											echo '<strong>A:</strong> ' . esc_html($awareness) . ' ';
											echo '<strong>O:</strong> ' . esc_html($operations) . ' ';
											echo '<strong>T:</strong> ' . esc_html($technician);

											if ($course->course_type === 'water' && isset($counts['surf_swiftwater'])) {
												echo ' <strong>S:</strong> ' . esc_html($counts['surf_swiftwater']);
											}
											?>
										</td>
										<td>
											<button type="button" class="button button-small edit-course" 
													data-course-id="<?php echo esc_attr($course->id); ?>">Edit</button>
											<button type="button" class="button button-small delete-course" 
													data-course-id="<?php echo esc_attr($course->id); ?>">Delete</button>
										</td>
									</tr>
								<?php endforeach; ?>
							<?php endif; ?>
						</tbody>
					</table>

					<script type="text/template" id="assistant-template">
						<div class="assistant-entry">
							<div class="assistant-fields">
								<input type="text" name="assistant_first_name[]" placeholder="First Name">
								<input type="text" name="assistant_last_name[]" placeholder="Last Name">
								<input type="email" name="assistant_email[]" placeholder="Email">
							</div>
							<button type="button" class="button remove-assistant">Remove</button>
						</div>
					</script>
				</div>
				<?php
			}

			private function process_course_assistants($course_id, $assistants) {
				global $wpdb;
				
				// First, remove existing assistant records for this course
				$wpdb->delete(
					$wpdb->prefix . 'lsim_assistant_history',
					['course_id' => $course_id],
					['%d']
				);
				
				$wpdb->delete(
					$wpdb->prefix . 'lsim_pending_assistants',
					['course_id' => $course_id],
					['%d']
				);

				foreach ($assistants as $assistant) {
					if (empty($assistant['email'])) {
						continue;
					}

					// Sanitize assistant data
					$assistant_data = [
						'email' => sanitize_email($assistant['email']),
						'first_name' => sanitize_text_field($assistant['first_name'] ?? ''),
						'last_name' => sanitize_text_field($assistant['last_name'] ?? '')
					];

					// Skip if email is invalid
					if (!is_email($assistant_data['email'])) {
						continue;
					}

					// Check if this email belongs to an existing instructor
					$instructor = get_posts([
						'post_type' => 'instructor',
						'meta_key' => '_email',
						'meta_value' => $assistant_data['email'],
						'posts_per_page' => 1
					]);

					if ($instructor) {
						// Add to confirmed assistants
						$wpdb->insert(
							$wpdb->prefix . 'lsim_assistant_history',
							[
								'instructor_id' => $instructor[0]->ID,
								'course_id' => $course_id,
								'created_at' => current_time('mysql')
							],
							['%d', '%d', '%s']
						);
					} else {
						// Add to pending assistants
						$wpdb->insert(
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
					}
				}
			}

			public function handle_save_course() {
				try {
					// Verify nonce and capabilities
					check_ajax_referer(LSIM_NONCE_ACTION, 'nonce');
					
					if (!current_user_can('edit_posts')) {
						throw new Exception('Unauthorized access');
					}

					global $wpdb;
					
					// Log incoming data for debugging
					error_log('LSIM: Course save attempt - POST data: ' . print_r($_POST, true));

					// Validate required fields
					$required_fields = ['instructor_id', 'course_type', 'course_date', 'location'];
					$data = [];
					
					foreach ($required_fields as $field) {
						if (empty($_POST[$field])) {
							throw new Exception("Missing required field: $field");
						}
						
						$data[$field] = $_POST[$field];
					}

					// Validate data types and formats
					$data['instructor_id'] = absint($data['instructor_id']);
					if ($data['instructor_id'] === 0) {
						throw new Exception('Invalid instructor ID');
					}

					// Validate course type
					if (!in_array($data['course_type'], ['ice', 'water'])) {
						throw new Exception('Invalid course type');
					}

					// Validate date format
					$course_date = strtotime($data['course_date']);
					if (!$course_date) {
						throw new Exception('Invalid date format');
					}
					$data['course_date'] = date('Y-m-d', $course_date);

					// Sanitize location
					$data['location'] = sanitize_text_field($data['location']);
					if (empty($data['location'])) {
						throw new Exception('Location cannot be empty');
					}

					// Validate and parse participants data
					if (!empty($_POST['participants_data'])) {
						$participants = json_decode(stripslashes($_POST['participants_data']), true);
						if (json_last_error() !== JSON_ERROR_NONE) {
							throw new Exception('Invalid participants data format');
						}

						// Ensure all participant counts are non-negative integers
						foreach (['awareness', 'operations', 'technician', 'surf_swiftwater'] as $key) {
							$participants[$key] = isset($participants[$key]) ? absint($participants[$key]) : 0;
						}
						$data['participants_data'] = wp_json_encode($participants);
					} else {
						$data['participants_data'] = wp_json_encode([
							'awareness' => 0,
							'operations' => 0,
							'technician' => 0,
							'surf_swiftwater' => 0
						]);
					}

					// Validate assistants if present
					if (isset($_POST['assistants']) && is_array($_POST['assistants'])) {
						foreach ($_POST['assistants'] as $assistant) {
							// Require all assistant fields
							if (empty($assistant['email']) || empty($assistant['first_name']) || empty($assistant['last_name'])) {
								throw new Exception('All assistant fields (email, first name, last name) are required');
							}

							// Validate email format
							$email = sanitize_email($assistant['email']);
							if (!is_email($email)) {
								throw new Exception('Invalid assistant email format: ' . esc_html($assistant['email']));
							}
						}
					}

					// Prepare database operation
					$course_data = [
						'instructor_id' => $data['instructor_id'],
						'course_type' => $data['course_type'],
						'course_date' => $data['course_date'],
						'location' => $data['location'],
						'participants_data' => $data['participants_data'],
						'modified_at' => current_time('mysql')
					];

					error_log('LSIM: Processed course data: ' . print_r($course_data, true));

					// Perform database operation
					if (!empty($_POST['course_id'])) {
						// Update existing course
						$course_id = absint($_POST['course_id']);
						
						$result = $wpdb->update(
							$wpdb->prefix . 'lsim_course_history',
							$course_data,
							['id' => $course_id],
							['%d', '%s', '%s', '%s', '%s', '%s'],
							['%d']
						);
					} else {
						// Insert new course
						$course_data['created_at'] = current_time('mysql');
						
						$result = $wpdb->insert(
							$wpdb->prefix . 'lsim_course_history',
							$course_data,
							['%d', '%s', '%s', '%s', '%s', '%s', '%s']
						);
						$course_id = $wpdb->insert_id;
					}

					if ($result === false) {
						throw new Exception($wpdb->last_error ?: 'Database error occurred');
					}

					// Process assistants if any
					if (isset($_POST['assistants']) && is_array($_POST['assistants'])) {
						$this->process_course_assistants($course_id, $_POST['assistants']);
					}

					wp_send_json_success([
						'message' => 'Course saved successfully',
						'course_id' => $course_id
					]);

				} catch (Exception $e) {
					error_log('LSIM: Course save error: ' . $e->getMessage());
					wp_send_json_error([
						'message' => 'Error saving course: ' . $e->getMessage()
					]);
				}
			}

			private function get_course_assistants($course_id) {
				global $wpdb;
				
				$registered = $wpdb->get_results($wpdb->prepare("
					SELECT 
						ah.instructor_id,
						pm1.meta_value as first_name,
						pm2.meta_value as last_name,
						pm3.meta_value as email,
						0 as pending
					FROM {$wpdb->prefix}lsim_assistant_history ah
					JOIN {$wpdb->posts} p ON ah.instructor_id = p.ID
					LEFT JOIN {$wpdb->postmeta} pm1 ON p.ID = pm1.post_id AND pm1.meta_key = '_first_name'
					LEFT JOIN {$wpdb->postmeta} pm2 ON p.ID = pm2.post_id AND pm2.meta_key = '_last_name'
					LEFT JOIN {$wpdb->postmeta} pm3 ON p.ID = pm3.post_id AND pm3.meta_key = '_email'
					WHERE ah.course_id = %d",
					$course_id
				));

				$pending = $wpdb->get_results($wpdb->prepare("
					SELECT 
						first_name,
						last_name,
						email,
						1 as pending
					FROM {$wpdb->prefix}lsim_pending_assistants
					WHERE course_id = %d",
					$course_id
				));

				return array_merge($registered ?: [], $pending ?: []);
			}

			private function get_course_data($course_id) {
				global $wpdb;
				
				$course = $wpdb->get_row($wpdb->prepare(
					"SELECT * FROM {$wpdb->prefix}lsim_course_history WHERE id = %d",
					$course_id
				));

				if (!$course) return null;

				$assistants = $this->get_course_assistants($course_id);
				
				return [
					'id' => $course->id,
					'course_date' => $course->course_date,
					'course_type' => $course->course_type,
					'location' => $course->location,
					'participants' => json_decode($course->participants_data, true),
					'assistants' => array_map(function($assistant) {
						return [
							'first_name' => $assistant->first_name,
							'last_name' => $assistant->last_name,
							'email' => $assistant->email,
							'pending' => isset($assistant->pending) ? (bool)$assistant->pending : false
						];
					}, $assistants)
				];
			}

			public function handle_delete_course() {
				check_ajax_referer(LSIM_NONCE_ACTION, 'nonce');
				
				if (!current_user_can('edit_posts')) {
					wp_send_json_error(['message' => 'Unauthorized']);
					return;
				}

				global $wpdb;
				$course_id = intval($_POST['course_id']);

				$result = $wpdb->delete(
					$wpdb->prefix . 'lsim_course_history',
					['id' => $course_id],
					['%d']
				);

				if ($result === false) {
					wp_send_json_error(['message' => 'Failed to delete course']);
					return;
				}

				// Clean up related records
				$wpdb->delete(
					$wpdb->prefix . 'lsim_assistant_history',
					['course_id' => $course_id],
					['%d']
				);

				$wpdb->delete(
							$wpdb->prefix . 'lsim_pending_assistants',
							['course_id' => $course_id],
							['%d']
						);

						wp_send_json_success(['message' => 'Course deleted successfully']);
					}
				}		