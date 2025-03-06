<?php
if (!defined('ABSPATH')) exit;

class LSIM_Import_Export {
    public function __construct() {
        add_action('admin_post_import_instructors', [$this, 'handle_import']);
        add_action('admin_post_export_data', [$this, 'handle_export']);
    }

	public function handle_import() {
    try {
        if (!isset($_POST['instructor_import_nonce']) || 
            !wp_verify_nonce($_POST['instructor_import_nonce'], 'import_instructors')) {
            wp_die('Invalid nonce');
        }

        if (!current_user_can('manage_options')) {
            wp_die('Unauthorized access');
        }

        if (!isset($_FILES['instructor_csv']) || $_FILES['instructor_csv']['error'] !== UPLOAD_ERR_OK) {
            throw new Exception('No file uploaded or upload error occurred');
        }

        $file = $_FILES['instructor_csv'];
        if ($file['type'] !== 'text/csv' && $file['type'] !== 'application/vnd.ms-excel') {
            throw new Exception('Invalid file type. Please upload a CSV file.');
        }

        $handle = fopen($file['tmp_name'], 'r');
        if (!$handle) {
            throw new Exception('Failed to open uploaded file');
        }

        // Skip header row if option is checked
        if (isset($_POST['skip_first_row']) && $_POST['skip_first_row']) {
            fgetcsv($handle);
        }

        $data_manager = LSIM_Data_Manager::get_instance();
        $import_stats = [
			'imported' => 0,
			'skipped' => 0,
			'errors' => 0,
			'error_messages' => [],
			'skipped_details' => []  // New array for tracking skips
		];

        while (($row = fgetcsv($handle)) !== false) {
            try {
                // Basic validation
                if (count($row) < 11) { // Minimum required columns
                    throw new Exception('Invalid row format - insufficient columns');
                }

                // Process instructor data
                $instructor_data = [
                    'first_name' => sanitize_text_field($row[0]),
                    'last_name' => sanitize_text_field($row[1]),
                    'department' => sanitize_text_field($row[2]),
                    'phone' => sanitize_text_field($row[3]),
                    'email' => sanitize_email($row[4]),
                    'state' => sanitize_text_field($row[5])
                ];

                // Check for existing instructor
                $existing = $data_manager->get_instructor_by_email($instructor_data['email']);
                if ($existing) {
					$instructor_id = $existing[0]->ID;
					$import_stats['skipped']++;
					$import_stats['skipped_details'][] = sprintf(
						'Skipped row for %s %s (email: %s) - Instructor already exists with ID: %s',
						$instructor_data['first_name'],
						$instructor_data['last_name'],
						$instructor_data['email'],
						$instructor_id
					);
				} else {
                    // Create new instructor
                    $instructor_id = $data_manager->save_instructor($instructor_data);
                    $import_stats['imported']++;
                }

                // Process certification data
                $cert_type = strtolower(sanitize_text_field($row[6]));
                if (!in_array($cert_type, ['ice', 'water'])) {
                    throw new Exception('Invalid certification type: ' . $cert_type);
                }

                $original_date = sanitize_text_field($row[7]);
                if ($original_date) {
                    update_post_meta($instructor_id, "_{$cert_type}_original_date", $original_date);
                }

                // Process reauth dates
                $reauth_dates = array_filter(array_map('sanitize_text_field', array_slice($row, 9, 4)));
                if (!empty($reauth_dates)) {
                    sort($reauth_dates);
                    update_post_meta($instructor_id, "_{$cert_type}_recert_dates", $reauth_dates);
                }

                // Process course data if present
                $course_date = sanitize_text_field($row[13]);
                if ($course_date) {
                    $course_data = [
                        'instructor_id' => $instructor_id,
                        'course_type' => $cert_type,
                        'course_date' => $course_date,
                        'location' => sanitize_text_field($row[14]),
                        'participants_data' => [
                            'awareness' => intval($row[17]),
                            'operations' => intval($row[18]),
                            'technician' => intval($row[19]),
                            'surf_swiftwater' => $cert_type === 'water' ? intval($row[20]) : 0
                        ]
                    ];

                    // Process assistant if provided
                    if (!empty($row[15]) && !empty($row[16])) {
                        $course_data['assistants'] = [[
                            'first_name' => sanitize_text_field(strtok($row[15], ' ')),
                            'last_name' => sanitize_text_field(strtok('')),
                            'email' => sanitize_email($row[16])
                        ]];
                    }

                    $data_manager->save_course($course_data);
                }

            } catch (Exception $e) {
                $import_stats['errors']++;
                $import_stats['error_messages'][] = sprintf(
                    'Error processing row for %s %s: %s',
                    $row[0] ?? 'unknown',
                    $row[1] ?? 'unknown',
                    $e->getMessage()
                );
                lsim_log_error('Import row error', [
                    'error' => $e->getMessage(),
                    'row' => $row
                ]);
                continue;
            }
        }

        fclose($handle);
        update_option('lsim_last_import_log', $import_stats);

        wp_redirect(add_query_arg(
            ['page' => 'instructor-import-export', 'import' => 'success'],
            admin_url('admin.php')
        ));
        exit;

    } catch (Exception $e) {
        lsim_log_error('Import process error', [
            'error' => $e->getMessage()
        ]);
        wp_die('Import failed: ' . esc_html($e->getMessage()));
    }
}


    public function render_import_export_page() {
        if (!current_user_can('manage_options')) {
            wp_die('Unauthorized access');
        }
        ?>
        <div class="wrap">
            <h1>Import/Export Instructors</h1>

            <!-- Import Section -->
            <div class="card">
                <h2>Import Instructors</h2>
                <div class="import-instructions">
                    <p>Upload a CSV file containing instructor information. The CSV should have the following columns:</p>
                    <ul style="list-style-type: disc; margin-left: 20px;">
                        <li>First Name, Last Name, Department/Agency, Phone</li>
                        <li>Certification Type (Ice or Water)</li>
                        <li>Original Auth Date (YYYY-MM-DD), Training Location</li>
                        <li>ReAuth Dates 1-4 (YYYY-MM-DD)</li>
                        <li>Course Details (up to 4 sets):
                            <ul>
                                <li>Course Date</li>
                                <li>Assistant Instructor Name</li>
                                <li>Assistant Instructor Email</li>
                                <li>Student Counts: Awareness, Operations, Technician</li>
                                <li>Surf/Swiftwater Count (Water certification only)</li>
                            </ul>
                        </li>
                    </ul>
                    
                    <p>
                        <a href="<?php echo plugin_dir_url(LSIM_PLUGIN_FILE) . 'includes/admin/templates/instructor-import-template.csv'; ?>" 
                           class="button">
                            Download Sample CSV Template
                        </a>
                    </p>
                </div>

                <form method="post" action="<?php echo admin_url('admin-post.php'); ?>" enctype="multipart/form-data">
                    <?php wp_nonce_field('import_instructors', 'instructor_import_nonce'); ?>
                    <input type="hidden" name="action" value="import_instructors">
                    
                    <p>
                        <label>
                            <input type="file" name="instructor_csv" accept=".csv" required>
                        </label>
                    </p>
                    
                    <p>
                        <label>
                            <input type="checkbox" name="skip_first_row" value="1" checked>
                            Skip first row (headers)
                        </label>
                    </p>
                    
                    <button type="submit" class="button button-primary">Import Instructors</button>
                </form>
            </div>

			<!-- Export Section -->
			<div class="card">
				<h2>Export Data</h2>
				<p>Export your instructor data in a comprehensive CSV format. The export includes:</p>
				<ul style="list-style-type: disc; margin-left: 20px;">
					<li>Basic instructor information (names, contact details, departments)</li>
					<li>Certification histories and dates</li>
					<li>Course histories with student counts</li>
					<li>Assistant instructor assignments</li>
				</ul>
				
				<p class="description">
					The exported file will be named with the current date and time and includes all instructor data in a single, consistent format.
				</p>

				<form method="post" action="<?php echo admin_url('admin-post.php'); ?>">
					<?php wp_nonce_field('export_data', 'export_nonce'); ?>
					<input type="hidden" name="action" value="export_data">
					<button type="submit" class="button button-primary">Export to CSV</button>
				</form>
			</div>

            <?php 
            // Show import results if any
            $import_log = get_option('lsim_last_import_log');
			if ($import_log): ?>
				<div class="card">
					<h2>Last Import Results</h2>
					<p>
						Imported: <?php echo esc_html($import_log['imported']); ?> instructors<br>
						Skipped: <?php echo esc_html($import_log['skipped']); ?> instructors<br>
						Errors: <?php echo esc_html($import_log['errors']); ?> instructors
					</p>
					
					<?php if (!empty($import_log['skipped_details'])): ?>
						<h3>Skipped Records:</h3>
						<div class="skipped-details" style="max-height: 200px; overflow-y: auto; padding: 10px; background: #f8f9fa; border-radius: 4px; margin-bottom: 15px;">
							<?php foreach ($import_log['skipped_details'] as $detail): ?>
								<p><?php echo esc_html($detail); ?></p>
							<?php endforeach; ?>
						</div>
					<?php endif; ?>
					
					<?php if (!empty($import_log['error_messages'])): ?>
						<h3>Error Details:</h3>
						<ul>
							<?php foreach ($import_log['error_messages'] as $error): ?>
								<li><?php echo esc_html($error); ?></li>
							<?php endforeach; ?>
						</ul>
					<?php endif; ?>
				</div>
			<?php endif; ?>
        </div>

        <style>
            .card {
                background: #fff;
                border: 1px solid #ccd0d4;
                border-radius: 4px;
                padding: 20px;
                margin-bottom: 20px;
                box-shadow: 0 1px 3px rgba(0,0,0,0.05);
            }
            .card h2 {
                margin-top: 0;
                padding-bottom: 10px;
                border-bottom: 1px solid #eee;
                color: #23282d;
            }
            .card h3 {
                margin: 1em 0 0.5em;
                font-size: 1.1em;
            }
            .import-instructions {
                background: #f8f9fa;
                padding: 15px;
                border-radius: 4px;
                margin-bottom: 20px;
            }
            .import-instructions ul {
                margin-bottom: 15px;
            }
            .import-instructions ul ul {
                margin-top: 5px;
                margin-left: 20px;
            }
            .export-options {
                margin: 15px 0;
            }
            .export-options label {
                display: block;
                margin: 8px 0;
            }
            .description {
                color: #666;
                font-style: italic;
                margin: 15px 0;
            }
            .button {
                margin-right: 10px;
            }
        </style>
        <?php
    }
public function handle_export() {
    check_admin_referer('export_data', 'export_nonce');

    if (!current_user_can('manage_options')) {
        wp_die('Unauthorized access');
    }

    $filename = 'instructor-data-' . date('Y-m-d-His') . '.csv';
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    header('Pragma: no-cache');
    header('Expires: 0');

    $output = fopen('php://output', 'w');

    // Combined header for all data
    fputcsv($output, [
        'Instructor ID',
        'First Name',
        'Last Name',
        'Email',
        'Phone',
        'Department',
        'State',
        'Certification Type',
        'Original Date',
        'ReAuth Date 1',
        'ReAuth Date 2',
        'ReAuth Date 3',
        'ReAuth Date 4',
        'Course Date',
        'Course Location',
        'Course Type',
        'Awareness Count',
        'Operations Count',
        'Technician Count',
        'Surf/Swiftwater Count',
        'Assistant Name',
        'Assistant Email'
    ]);

    global $wpdb;
    
    // Get all instructors
    $instructors = get_posts([
        'post_type' => 'instructor',
        'posts_per_page' => -1,
        'post_status' => 'any'
    ]);

    foreach ($instructors as $instructor) {
        $instructor_id = get_post_meta($instructor->ID, '_instructor_id', true) ?: $instructor->ID;
        $first_name = get_post_meta($instructor->ID, '_first_name', true);
        $last_name = get_post_meta($instructor->ID, '_last_name', true);
        $email = get_post_meta($instructor->ID, '_email', true);
        $phone = get_post_meta($instructor->ID, '_phone', true);
        $department = get_post_meta($instructor->ID, '_department', true);
        $state = get_post_meta($instructor->ID, '_state', true);

        // Get certification data
        foreach (['ice', 'water'] as $cert_type) {
            $original_date = get_post_meta($instructor->ID, "_{$cert_type}_original_date", true);
            $recert_dates = get_post_meta($instructor->ID, "_{$cert_type}_recert_dates", true) ?: [];

            // Get courses for this certification type
            $courses = $wpdb->get_results($wpdb->prepare(
                "SELECT * FROM {$wpdb->prefix}lsim_course_history 
                WHERE instructor_id = %d AND course_type = %s 
                ORDER BY course_date",
                $instructor->ID,
                $cert_type
            ));

            if ($courses) {
                foreach ($courses as $course) {
                    $participants = json_decode($course->participants_data, true) ?: [];
                    
                    // Get assistants for this course
                    $assistants = $this->get_course_assistants($course->id);
                    
                    if (empty($assistants)) {
                        // Output row with no assistant
                        $this->write_export_row($output, [
                            $instructor_id,
                            $first_name,
                            $last_name,
                            $email,
                            $phone,
                            $department,
                            $state,
                            ucfirst($cert_type) . ' Rescue',
                            $original_date,
                            $recert_dates[0] ?? '',
                            $recert_dates[1] ?? '',
                            $recert_dates[2] ?? '',
                            $recert_dates[3] ?? '',
                            $this->format_date($course->course_date),
                            $course->location,
                            $cert_type,
                            $participants['awareness'] ?? 0,
                            $participants['operations'] ?? 0,
                            $participants['technician'] ?? 0,
                            $cert_type === 'water' ? ($participants['surf_swiftwater'] ?? 0) : 'N/A',
                            '',
                            ''
                        ]);
                    } else {
                        foreach ($assistants as $assistant) {
                            $this->write_export_row($output, [
                                $instructor_id,
                                $first_name,
                                $last_name,
                                $email,
                                $phone,
                                $department,
                                $state,
                                ucfirst($cert_type) . ' Rescue',
                                $original_date,
                                $recert_dates[0] ?? '',
                                $recert_dates[1] ?? '',
                                $recert_dates[2] ?? '',
                                $recert_dates[3] ?? '',
                                $this->format_date($course->course_date),
                                $course->location,
                                $cert_type,
                                $participants['awareness'] ?? 0,
                                $participants['operations'] ?? 0,
                                $participants['technician'] ?? 0,
                                $cert_type === 'water' ? ($participants['surf_swiftwater'] ?? 0) : 'N/A',
                                $assistant->first_name . ' ' . $assistant->last_name,
                                $assistant->email
                            ]);
                        }
                    }
                }
            } else {
                // Output certification data even without courses
                if ($original_date || !empty($recert_dates)) {
                    $this->write_export_row($output, [
                        $instructor_id,
                        $first_name,
                        $last_name,
                        $email,
                        $phone,
                        $department,
                        $state,
                        ucfirst($cert_type) . ' Rescue',
                        $original_date,
                        $recert_dates[0] ?? '',
                        $recert_dates[1] ?? '',
                        $recert_dates[2] ?? '',
                        $recert_dates[3] ?? '',
                        '',
                        '',
                        '',
                        '',
                        '',
                        '',
                        '',
                        '',
                        ''
                    ]);
                }
            }
        }
    }

    fclose($output);
    exit;
}

private function format_date($date) {
    return $date !== '0000-00-00' ? date('Y-m-d', strtotime($date)) : '';
}

private function write_export_row($handle, $data) {
    foreach ($data as &$field) {
        if ($field === null) {
            $field = '';
        }
    }
    fputcsv($handle, $data);
}

private function get_course_assistants($course_id) {
    global $wpdb;
    
    return $wpdb->get_results($wpdb->prepare("
        SELECT 
            DISTINCT
            COALESCE(pm1.meta_value, pa.first_name) as first_name,
            COALESCE(pm2.meta_value, pa.last_name) as last_name,
            COALESCE(pm3.meta_value, pa.email) as email
        FROM (
            SELECT 
                ah.instructor_id,
                NULL as first_name,
                NULL as last_name,
                NULL as email
            FROM {$wpdb->prefix}lsim_assistant_history ah
            WHERE ah.course_id = %d
            UNION ALL
            SELECT 
                NULL as instructor_id,
                first_name,
                last_name,
                email
            FROM {$wpdb->prefix}lsim_pending_assistants
            WHERE course_id = %d
        ) AS combined
        LEFT JOIN {$wpdb->posts} p ON combined.instructor_id = p.ID
        LEFT JOIN {$wpdb->postmeta} pm1 ON p.ID = pm1.post_id AND pm1.meta_key = '_first_name'
        LEFT JOIN {$wpdb->postmeta} pm2 ON p.ID = pm2.post_id AND pm2.meta_key = '_last_name'
        LEFT JOIN {$wpdb->postmeta} pm3 ON p.ID = pm3.post_id AND pm3.meta_key = '_email'
        LEFT JOIN {$wpdb->prefix}lsim_pending_assistants pa ON 
            combined.first_name = pa.first_name AND 
            combined.last_name = pa.last_name AND 
            combined.email = pa.email
        WHERE COALESCE(pm1.meta_value, pa.first_name) IS NOT NULL
    ", $course_id, $course_id));
}
}