<?php
if (!defined('ABSPATH')) exit;

class LSIM_Reporting {
    public function __construct() {
        add_action('admin_enqueue_scripts', [$this, 'enqueue_assets']);
        add_action('wp_ajax_export_report', [$this, 'handle_report_export']);
    }

    public function enqueue_assets($hook) {
        if ($hook !== 'lifesaving-resources_page_instructor-reports') {
            return;
        }

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

        wp_localize_script('lsim-report-script', 'lsimReporting', [
            'nonce' => wp_create_nonce('lsim_reporting'),
            'ajaxurl' => admin_url('admin-ajax.php')
        ]);
    }

private function get_statistics($start_date, $end_date, $cert_type) {
        global $wpdb;

        $where_clause = $wpdb->prepare(
            "WHERE ch.course_date BETWEEN %s AND %s",
            $start_date,
            $end_date
        );

        if ($cert_type !== 'all') {
            $where_clause .= $wpdb->prepare(
                " AND ch.course_type = %s",
                $cert_type
            );
        }

        // Get course data
        $courses = $wpdb->get_results("
            SELECT 
                ch.*,
                p.post_title as instructor_name,
                pm.meta_value as instructor_id
            FROM {$wpdb->prefix}lsim_course_history ch
            JOIN {$wpdb->posts} p ON ch.instructor_id = p.ID
            LEFT JOIN {$wpdb->postmeta} pm ON p.ID = pm.post_id AND pm.meta_key = '_instructor_id'
            $where_clause
            ORDER BY ch.course_date DESC
        ");

        // Initialize statistics
        $stats = [
            'total_courses' => 0,
            'ice_courses' => 0,
            'water_courses' => 0,
            'students' => [
                'awareness' => 0,
                'operations' => 0,
                'technician' => 0,
                'surf_swiftwater' => 0
            ],
            'instructor_activity' => [],
            'assistant_summary' => [
                'total_registered' => 0,
                'total_pending' => 0,
                'courses_with_assistants' => 0,
                'top_assistants' => []
            ],
            'course_history' => [],
            'pending_assistants' => []
        ];

        // Process courses and collect course IDs
        $course_ids = [];
        foreach ($courses as $course) {
            $course_ids[] = $course->id;
            $stats['total_courses']++;
            $stats['ice_courses'] += ($course->course_type === 'ice') ? 1 : 0;
            $stats['water_courses'] += ($course->course_type === 'water') ? 1 : 0;

            $participants = json_decode($course->participants_data, true);
            $stats['students']['awareness'] += $participants['awareness'] ?? 0;
            $stats['students']['operations'] += $participants['operations'] ?? 0;
            $stats['students']['technician'] += $participants['technician'] ?? 0;
            $stats['students']['surf_swiftwater'] += $participants['surf_swiftwater'] ?? 0;

            $this->update_instructor_stats($stats['instructor_activity'], $course, $participants);
        }

        // Get registered assistants for these courses
        if (!empty($course_ids)) {
            // REPLACE THIS SECTION with the new query
            $registered_assistants = $wpdb->get_results("
                SELECT 
                    ah.id,
                    ah.instructor_id,
                    ch.course_date,
                    ch.course_type,
                    ch.location,
                    CONCAT(pm1.meta_value, ' ', pm2.meta_value) as assistant_name,
                    pm3.meta_value as assistant_email,
                    pm4.meta_value as assistant_id
                FROM {$wpdb->prefix}lsim_assistant_history ah
                JOIN {$wpdb->prefix}lsim_course_history ch ON ah.course_id = ch.id
                JOIN {$wpdb->posts} p ON ah.instructor_id = p.ID
                LEFT JOIN {$wpdb->postmeta} pm1 ON p.ID = pm1.post_id AND pm1.meta_key = '_first_name'
                LEFT JOIN {$wpdb->postmeta} pm2 ON p.ID = pm2.post_id AND pm2.meta_key = '_last_name'
                LEFT JOIN {$wpdb->postmeta} pm3 ON p.ID = pm3.post_id AND pm3.meta_key = '_email'
                LEFT JOIN {$wpdb->postmeta} pm4 ON p.ID = pm4.post_id AND pm4.meta_key = '_instructor_id'
                WHERE ah.course_id IN (" . implode(',', array_map('intval', $course_ids)) . ")
                ORDER BY ch.course_date DESC
            ");

            // Get pending assistants
            $pending_assistants = $wpdb->get_results("
                SELECT 
                    pa.*,
                    ch.course_date,
                    ch.course_type,
                    ch.location
                FROM {$wpdb->prefix}lsim_pending_assistants pa
                JOIN {$wpdb->prefix}lsim_course_history ch ON pa.course_id = ch.id
                WHERE pa.course_id IN (" . implode(',', array_map('intval', $course_ids)) . ")
                ORDER BY ch.course_date DESC
            ");

            $stats['assistant_summary']['total_registered'] = count($registered_assistants);
            $stats['assistant_summary']['total_pending'] = count($pending_assistants);
            $stats['assistant_summary']['courses_with_assistants'] = 
                count(array_unique(array_merge(
                    array_column($registered_assistants, 'course_id'),
                    array_column($pending_assistants, 'course_id')
                )));

            // Process courses with assistant information
            foreach ($courses as $course) {
                $course_assistants = array_filter($registered_assistants, function($a) use ($course) {
                    return $a->course_id == $course->id;
                });
                
                $course_pending = array_filter($pending_assistants, function($a) use ($course) {
                    return $a->course_id == $course->id;
                });

                $stats['course_history'][] = [
                    'course' => $course,
                    'registered_assistants' => $course_assistants,
                    'pending_assistants' => $course_pending
                ];
            }

            // Get top assistants
            $stats['assistant_summary']['top_assistants'] = $this->get_top_assistants($registered_assistants);
        }

        return $stats;
    }

    private function update_instructor_stats(&$instructor_activity, $course, $participants) {
        $instructor_id = $course->instructor_id;
        if (!isset($instructor_activity[$instructor_id])) {
            $instructor_activity[$instructor_id] = [
                'instructor_id' => $instructor_id,
                'instructor_name' => $course->instructor_name,
                'courses' => [
                    'ice' => 0,
                    'water' => 0
                ],
                'students' => [
                    'awareness' => 0,
                    'operations' => 0,
                    'technician' => 0,
                    'surf_swiftwater' => 0
                ],
                'last_course' => $course->course_date
            ];
        }

        $instructor_activity[$instructor_id]['courses'][$course->course_type]++;
        foreach ($participants as $level => $count) {
            $instructor_activity[$instructor_id]['students'][$level] += $count;
        }

        if (strtotime($course->course_date) > strtotime($instructor_activity[$instructor_id]['last_course'])) {
            $instructor_activity[$instructor_id]['last_course'] = $course->course_date;
        }
    }

    private function get_top_assistants($registered_assistants) {
        $assistant_counts = [];
        foreach ($registered_assistants as $assistant) {
            $key = $assistant->instructor_id;
            if (!isset($assistant_counts[$key])) {
                $assistant_counts[$key] = [
                    'id' => $assistant->assistant_id,
                    'name' => $assistant->assistant_name,
                    'email' => $assistant->assistant_email,
                    'count' => 0
                ];
            }
            $assistant_counts[$key]['count']++;
        }

        usort($assistant_counts, function($a, $b) {
            return $b['count'] - $a['count'];
        });

        return array_slice($assistant_counts, 0, 5);
    }

public function render_reports_page() {
        // Get filter parameters
        $start_date = isset($_GET['start_date']) ? sanitize_text_field($_GET['start_date']) : date('Y-m-d', strtotime('-1 year'));
        $end_date = isset($_GET['end_date']) ? sanitize_text_field($_GET['end_date']) : date('Y-m-d');
        $cert_type = isset($_GET['cert_type']) ? sanitize_text_field($_GET['cert_type']) : 'all';

        // Get statistics
        $stats = $this->get_statistics($start_date, $end_date, $cert_type);
        ?>
        <div class="wrap">
            <h1>Instructor Reports</h1>

            <!-- Filters -->
            <div class="report-filters card">
                <form method="get" class="filter-form">
                    <input type="hidden" name="page" value="instructor-reports">
                    <div class="filter-row">
                        <div class="filter-group">
                            <label>Date Range:</label>
                            <div class="date-inputs">
                                <input type="date" name="start_date" value="<?php echo esc_attr($start_date); ?>">
                                <span>to</span>
                                <input type="date" name="end_date" value="<?php echo esc_attr($end_date); ?>">
                            </div>
                        </div>
                        
                        <div class="filter-group">
                            <label>Certification Type:</label>
                            <select name="cert_type">
                                <option value="all" <?php selected($cert_type, 'all'); ?>>All Types</option>
                                <option value="ice" <?php selected($cert_type, 'ice'); ?>>Ice Rescue</option>
                                <option value="water" <?php selected($cert_type, 'water'); ?>>Water Rescue</option>
                            </select>
                        </div>

                        <div class="filter-actions">
                            <button type="submit" class="button button-primary">Apply Filters</button>
                            <button type="button" class="button" id="export-report" 
                                    data-start="<?php echo esc_attr($start_date); ?>"
                                    data-end="<?php echo esc_attr($end_date); ?>"
                                    data-type="<?php echo esc_attr($cert_type); ?>">
                                Export Report
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Summary Cards -->
            <div class="report-grid">
                <!-- Course Summary -->
                <div class="report-card">
                    <h3>Course Summary</h3>
                    <div class="stat-grid">
                        <div class="stat-item">
                            <span class="stat-label">Total Courses</span>
                            <span class="stat-value"><?php echo $stats['total_courses']; ?></span>
                        </div>
                        <div class="stat-item">
                            <span class="stat-label">Ice Rescue</span>
                            <span class="stat-value"><?php echo $stats['ice_courses']; ?></span>
                        </div>
                        <div class="stat-item">
                            <span class="stat-label">Water Rescue</span>
                            <span class="stat-value"><?php echo $stats['water_courses']; ?></span>
                        </div>
                    </div>
                </div>

                <!-- Student Summary -->
                <div class="report-card">
                    <h3>Students Certified</h3>
                    <div class="stat-grid">
                        <div class="stat-item">
                            <span class="stat-label">Awareness</span>
                            <span class="stat-value"><?php echo $stats['students']['awareness']; ?></span>
                        </div>
                        <div class="stat-item">
                            <span class="stat-label">Operations</span>
                            <span class="stat-value"><?php echo $stats['students']['operations']; ?></span>
                        </div>
                        <div class="stat-item">
                            <span class="stat-label">Technician</span>
                            <span class="stat-value"><?php echo $stats['students']['technician']; ?></span>
                        </div>
                        <?php if ($cert_type !== 'ice'): ?>
                            <div class="stat-item">
                                <span class="stat-label">Surf/Swiftwater</span>
                                <span class="stat-value"><?php echo $stats['students']['surf_swiftwater']; ?></span>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Assistant Summary -->
                <div class="report-card">
                    <h3>Assistant Summary</h3>
                    <div class="stat-grid">
                        <div class="stat-item">
                            <span class="stat-label">Total Assistants</span>
                            <span class="stat-value primary">
                                <?php echo $stats['assistant_summary']['total_registered']; ?>
                                <?php if ($stats['assistant_summary']['total_pending'] > 0): ?>
                                    <span class="pending-count">(+<?php echo $stats['assistant_summary']['total_pending']; ?> pending)</span>
                                <?php endif; ?>
                            </span>
                        </div>
                        <div class="stat-item">
                            <span class="stat-label">Courses with Assistants</span>
                            <span class="stat-value"><?php echo $stats['assistant_summary']['courses_with_assistants']; ?></span>
                        </div>
                    </div>

                    <?php if (!empty($stats['assistant_summary']['top_assistants'])): ?>
                        <div class="top-assistants">
                            <h4>Top Assistant Instructors</h4>
                            <table class="widefat striped">
                                <thead>
                                    <tr>
                                        <th>Assistant</th>
                                        <th>ID</th>
                                        <th>Courses Assisted</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($stats['assistant_summary']['top_assistants'] as $assistant): ?>
                                        <tr>
                                            <td><?php echo esc_html($assistant['name']); ?></td>
                                            <td><?php echo esc_html($assistant['id']); ?></td>
                                            <td><?php echo esc_html($assistant['count']); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Detailed Reports Tabs -->
            <div class="tabbed-reports card">
                <ul class="tab-nav">
                    <li class="active" data-tab="course-history">Course History</li>
                    <li data-tab="instructor-activity">Instructor Activity</li>
                    <li data-tab="assistant-details">Assistant Details</li>
                </ul>

                <!-- Course History Tab -->
                <div class="tab-content active" id="course-history">
                    <table class="widefat striped">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Instructor</th>
                                <th>Type</th>
                                <th>Location</th>
                                <th>Students</th>
                                <th>Assistants</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($stats['course_history'] as $entry): ?>
                                <tr>
                                    <td><?php echo esc_html(date('M j, Y', strtotime($entry['course']->course_date))); ?></td>
                                    <td>
                                        <?php 
                                        echo esc_html($entry['course']->instructor_name);
                                        if ($entry['course']->instructor_id) {
                                            echo ' (ID: ' . esc_html($entry['course']->instructor_id) . ')';
                                        }
                                        ?>
                                    </td>
                                    <td><?php echo esc_html(ucfirst($entry['course']->course_type)); ?></td>
                                    <td><?php echo esc_html($entry['course']->location); ?></td>
                                    <td>
                                        <?php
                                        $participants = json_decode($entry['course']->participants_data, true);
                                        echo 'A: ' . esc_html($participants['awareness']) . '<br>';
                                        echo 'O: ' . esc_html($participants['operations']) . '<br>';
                                        echo 'T: ' . esc_html($participants['technician']);
                                        if (isset($participants['surf_swiftwater']) && $participants['surf_swiftwater'] > 0) {
                                            echo '<br>S: ' . esc_html($participants['surf_swiftwater']);
                                        }
                                        ?>
                                    </td>
                                    <td>
                                        <?php 
                                        foreach ($entry['registered_assistants'] as $assistant) {
                                            echo '<div class="assistant registered">';
                                            echo esc_html($assistant->assistant_name);
                                            if ($assistant->assistant_id) {
                                                echo ' (ID: ' . esc_html($assistant->assistant_id) . ')';
                                            }
                                            echo '</div>';
                                        }
                                        foreach ($entry['pending_assistants'] as $assistant) {
                                            echo '<div class="assistant pending">';
                                            echo esc_html($assistant->first_name . ' ' . $assistant->last_name);
                                            echo ' <span class="pending-badge">Pending</span>';
                                            echo '</div>';
                                        }
                                        if (empty($entry['registered_assistants']) && empty($entry['pending_assistants'])) {
                                            echo 'None';
                                        }
                                        ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Instructor Activity Tab -->
                <div class="tab-content" id="instructor-activity">
                    <table class="widefat striped">
                        <thead>
                            <tr>
                                <th>Instructor ID</th>
                                <th>Name</th>
                                <th>Ice Courses</th>
                                <th>Water Courses</th>
                                <th>Total Students</th>
                                <th>Last Course</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($stats['instructor_activity'] as $activity): ?>
                                <tr>
                                    <td><?php echo esc_html($activity['instructor_id']); ?></td>
                                    <td><?php echo esc_html($activity['instructor_name']); ?></td>
                                    <td><?php echo esc_html($activity['courses']['ice']); ?></td>
                                    <td><?php echo esc_html($activity['courses']['water']); ?></td>
                                    <td>
                                        <?php
                                        $total = array_sum($activity['students']);
                                        echo esc_html($total);
                                        ?>
                                        <span class="details-toggle" title="Show Details">ⓘ</span>
                                        <div class="student-details">
                                            Awareness: <?php echo esc_html($activity['students']['awareness']); ?><br>
                                            Operations: <?php echo esc_html($activity['students']['operations']); ?><br>
                                            Technician: <?php echo esc_html($activity['students']['technician']); ?><br>
                                            <?php if ($activity['students']['surf_swiftwater'] > 0): ?>
                                                Surf/Swiftwater: <?php echo esc_html($activity['students']['surf_swiftwater']); ?>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                    <td><?php echo esc_html(date('M j, Y', strtotime($activity['last_course']))); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Assistant Details Tab -->
                <div class="tab-content" id="assistant-details">
                    <div class="assistant-filters">
                        <label>
                            <input type="checkbox" class="toggle-pending" checked> Show Pending Assistants
                        </label>
                    </div>
                    <table class="widefat striped">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Assistant</th>
                                <th>Status</th>
                                <th>Course Type</th>
                                <th>Location</th>
                                <th>Lead Instructor</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($stats['course_history'] as $entry): ?>
                                <?php foreach ($entry['registered_assistants'] as $assistant): ?>
                                    <tr class="assistant-row registered">
                                        <td><?php echo esc_html(date('M j, Y', strtotime($entry['course']->course_date))); ?></td>
                                        <td>
                                            <?php 
                                            echo esc_html($assistant->assistant_name);
                                            if ($assistant->assistant_id) {
                                                echo ' (ID: ' . esc_html($assistant->assistant_id) . ')';
                                            }
                                            ?>
                                        </td>
                                        <td><span class="status-badge registered">Registered</span></td>
                                        <td><?php echo esc_html(ucfirst($entry['course']->course_type)); ?></td>
                                        <td><?php echo esc_html($entry['course']->location); ?></td>
                                        <td><?php echo esc_html($entry['course']->instructor_name); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                                <?php foreach ($entry['pending_assistants'] as $assistant): ?>
                                    <tr class="assistant-row pending">
                                        <td><?php echo esc_html(date('M j, Y', strtotime($entry['course']->course_date))); ?></td>
                                        <td>
                                            <?php echo esc_html($assistant->first_name . ' ' . $assistant->last_name); ?>
                                            <br>
                                            <small><?php echo esc_html($assistant->email); ?></small>
                                        </td>
                                        <td><span class="status-badge pending">Pending</span></td>
                                        <td><?php echo esc_html(ucfirst($entry['course']->course_type)); ?></td>
                                        <td><?php echo esc_html($entry['course']->location); ?></td>
                                        <td><?php echo esc_html($entry['course']->instructor_name); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <script>
            jQuery(document).ready(function($) {
                // Tab switching
                $('.tab-nav li').click(function() {
                    $('.tab-nav li').removeClass('active');
                    $('.tab-content').removeClass('active');
                    
                    $(this).addClass('active');
                    $('#' + $(this).data('tab')).addClass('active');
                });

                // Toggle student details
                $('.details-toggle').hover(
                    function() {
                        $(this).next('.student-details').show();
                    },
                    function() {
                        $(this).next('.student-details').hide();
                    }
                );

                // Toggle pending assistants
                $('.toggle-pending').change(function() {
                    $('.assistant-row.pending').toggle(this.checked);
                });

                // Export report
                $('#export-report').click(function() {
                    const button = $(this);
                    const data = {
                        action: 'export_report',
                        nonce: lsimReporting.nonce,
                        start_date: button.data('start'),
                        end_date: button.data('end'),
                        cert_type: button.data('type')
                    };

                    button.prop('disabled', true).text('Exporting...');

                    $.ajax({
                        url: lsimReporting.ajaxurl,
                        type: 'POST',
                        data: data,
                        success: function(response) {
                            if (response.success) {
                                const blob = new Blob([response.data], { type: 'text/csv' });
                                const url = window.URL.createObjectURL(blob);
                                const a = document.createElement('a');
                                a.style.display = 'none';
                                a.href = url;
                                a.download = 'instructor-report.csv';
                                document.body.appendChild(a);
                                a.click();
                                window.URL.revokeObjectURL(url);
                                document.body.removeChild(a);
                            } else {
                                alert('Error exporting report');
                            }
                        },
                        error: function() {
                            alert('Error exporting report');
                        },
                        complete: function() {
                            button.prop('disabled', false).text('Export Report');
                        }
                    });
                });
            });
        </script>
        <?php
    }

    public function handle_report_export() {
        check_ajax_referer('lsim_reporting', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_send_json_error('Unauthorized');
        }

        $start_date = sanitize_text_field($_POST['start_date']);
        $end_date = sanitize_text_field($_POST['end_date']);
        $cert_type = sanitize_text_field($_POST['cert_type']);

        $stats = $this->get_statistics($start_date, $end_date, $cert_type);
        $csv_data = $this->generate_csv_report($stats);

        wp_send_json_success($csv_data);
    }

    private function generate_csv_report($stats) {
        $csv = [];
        
        // Summary section
        $csv[] = ['Summary Statistics'];
        $csv[] = ['Total Courses', $stats['total_courses']];
        $csv[] = ['Ice Rescue Courses', $stats['ice_courses']];
        $csv[] = ['Water Rescue Courses', $stats['water_courses']];
        $csv[] = [];
        
        // Student statistics
        $csv[] = ['Student Certifications'];
        foreach ($stats['students'] as $level => $count) {
            $csv[] = [ucfirst($level), $count];
        }
        $csv[] = [];
        
        // Course history
        $csv[] = ['Course History'];
        $csv[] = ['Date', 'Instructor', 'Type', 'Location', 'Students', 'Assistants'];
        foreach ($stats['course_history'] as $entry) {
            $students = json_decode($entry['course']->participants_data, true);
            $student_counts = "A:{$students['awareness']} O:{$students['operations']} T:{$students['technician']}";
            if (isset($students['surf_swiftwater']) && $students['surf_swiftwater'] > 0) {
                $student_counts .= " S:{$students['surf_swiftwater']}";
            }

            $assistants = [];
            foreach ($entry['registered_assistants'] as $assistant) {
                $assistants[] = $assistant->assistant_name . ' (Registered)';
            }
            foreach ($entry['pending_assistants'] as $assistant) {
                $assistants[] = $assistant->first_name . ' ' . $assistant->last_name . ' (Pending)';
            }

            $csv[] = [
                date('Y-m-d', strtotime($entry['course']->course_date)),
                $entry['course']->instructor_name,
                ucfirst($entry['course']->course_type),
                $entry['course']->location,
                $student_counts,
                implode('; ', $assistants)
            ];
        }

        // Convert to CSV string
        $output = fopen('php://temp', 'r+');
        foreach ($csv as $row) {
            fputcsv($output, $row);
        }
        rewind($output);
        $csv_string = stream_get_contents($output);
        fclose($output);

        return $csv_string;
    }
}