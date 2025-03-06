<?php
if (!defined('ABSPATH')) exit;

class LSIM_Assistant_Tracking {
    public function __construct() {
        add_action('add_meta_boxes', [$this, 'add_meta_boxes']);
        add_action('save_post_instructor', [$this, 'save_assistant_data']);
        add_filter('manage_instructor_posts_columns', [$this, 'add_assistant_column']);
        add_action('manage_instructor_posts_custom_column', [$this, 'display_assistant_column'], 10, 2);
    }

    public function add_meta_boxes() {
        add_meta_box(
            'assistant_history',
            'Assistant Teaching History',
            [$this, 'render_assistant_history'],
            'instructor',
            'normal',
            'default'
        );
    }

    public function save_assistant_data($post_id) {
        if (!isset($_POST['assistant_history_nonce']) || 
            !wp_verify_nonce($_POST['assistant_history_nonce'], 'save_assistant_history')) {
            return;
        }

        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }

        if (!current_user_can('edit_post', $post_id)) {
            return;
        }
    }

    public function render_assistant_history($post) {
        global $wpdb;
        wp_nonce_field('save_assistant_history', 'assistant_history_nonce');

        // Get instructor's email
        $email = get_post_meta($post->ID, '_email', true);
        
        // First get registered assistant history
        $assistant_history = $wpdb->get_results($wpdb->prepare("
            SELECT 
                ah.course_id,
                ch.course_date,
                ch.course_type,
                ch.location,
                p.post_title as lead_instructor_name,
                p.ID as lead_instructor_id,
                pm1.meta_value as lead_instructor_email,
                pm2.meta_value as lead_instructor_phone,
                'Registered' as status
            FROM {$wpdb->prefix}lsim_assistant_history ah
            JOIN {$wpdb->prefix}lsim_course_history ch ON ah.course_id = ch.id
            JOIN {$wpdb->posts} p ON ch.instructor_id = p.ID
            LEFT JOIN {$wpdb->postmeta} pm1 ON p.ID = pm1.post_id AND pm1.meta_key = '_email'
            LEFT JOIN {$wpdb->postmeta} pm2 ON p.ID = pm2.post_id AND pm2.meta_key = '_phone'
            WHERE ah.instructor_id = %d
            ORDER BY ch.course_date DESC",
            $post->ID
        ));

        // Then get any remaining pending assignments
        $pending_history = $wpdb->get_results($wpdb->prepare("
            SELECT 
                pa.course_id,
                ch.course_date,
                ch.course_type,
                ch.location,
                p.post_title as lead_instructor_name,
                p.ID as lead_instructor_id,
                pm1.meta_value as lead_instructor_email,
                pm2.meta_value as lead_instructor_phone,
                'Pending Registration' as status
            FROM {$wpdb->prefix}lsim_pending_assistants pa
            JOIN {$wpdb->prefix}lsim_course_history ch ON pa.course_id = ch.id
            JOIN {$wpdb->posts} p ON ch.instructor_id = p.ID
            LEFT JOIN {$wpdb->postmeta} pm1 ON p.ID = pm1.post_id AND pm1.meta_key = '_email'
            LEFT JOIN {$wpdb->postmeta} pm2 ON p.ID = pm2.post_id AND pm2.meta_key = '_phone'
            WHERE pa.email = %s 
            AND NOT EXISTS (
                SELECT 1 
                FROM {$wpdb->prefix}lsim_assistant_history ah 
                WHERE ah.course_id = pa.course_id 
                AND ah.instructor_id = %d
            )
            ORDER BY ch.course_date DESC",
            $email,
            $post->ID
        ));

        // Merge and sort all history
        $all_history = array_merge($assistant_history, $pending_history);
        usort($all_history, function($a, $b) {
            return strtotime($b->course_date) - strtotime($a->course_date);
        });
        ?>
<div class="assistant-history-wrapper">
            <?php if (empty($all_history)): ?>
                <p class="no-history">No assistant teaching history recorded yet.</p>
            <?php else: ?>
                <table class="widefat striped">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Lead Instructor</th>
                            <th>Contact Info</th>
                            <th>Course Type</th>
                            <th>Location</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($all_history as $history): ?>
                            <tr>
                                <td><?php echo esc_html(date('M j, Y', strtotime($history->course_date))); ?></td>
                                <td>
                                    <?php 
                                    echo esc_html($history->lead_instructor_name);
                                    $instructor_id = get_post_meta($history->lead_instructor_id, '_instructor_id', true);
                                    if ($instructor_id) {
                                        echo ' (ID: ' . esc_html($instructor_id) . ')';
                                    }
                                    ?>
                                </td>
                                <td>
                                    <?php if ($history->lead_instructor_email): ?>
                                        Email: <?php echo esc_html($history->lead_instructor_email); ?><br>
                                    <?php endif; ?>
                                    <?php if ($history->lead_instructor_phone): ?>
                                        Phone: <?php echo esc_html($history->lead_instructor_phone); ?>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo esc_html(ucfirst($history->course_type)); ?></td>
                                <td><?php echo esc_html($history->location); ?></td>
                                <td>
                                    <span class="status-badge status-<?php echo strtolower(str_replace(' ', '-', $history->status)); ?>">
                                        <?php echo esc_html($history->status); ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

                <div class="assistant-stats">
                    <h4>Summary Statistics</h4>
                    <?php
                    $total_courses = count($assistant_history); // Only count confirmed courses
                    $ice_courses = count(array_filter($assistant_history, function($h) {
                        return $h->course_type === 'ice';
                    }));
                    $water_courses = count(array_filter($assistant_history, function($h) {
                        return $h->course_type === 'water';
                    }));
                    $pending_courses = count($pending_history);
                    ?>
                    <p>
                        Confirmed Courses Assisted: <?php echo esc_html($total_courses); ?><br>
                        Ice Rescue Courses: <?php echo esc_html($ice_courses); ?><br>
                        Water Rescue Courses: <?php echo esc_html($water_courses); ?><br>
                        <?php if ($pending_courses > 0): ?>
                            Pending Course Registrations: <?php echo esc_html($pending_courses); ?>
                        <?php endif; ?>
                    </p>
                </div>
            <?php endif; ?>

            <style>
                .assistant-history-wrapper { margin: 15px 0; }
                .assistant-stats {
                    margin-top: 20px;
                    padding: 15px;
                    background: #f8f9fa;
                    border: 1px solid #ddd;
                    border-radius: 4px;
                }
                .assistant-stats h4 { margin: 0 0 10px 0; }
                .no-history {
                    padding: 20px;
                    background: #f8f9fa;
                    border: 1px solid #ddd;
                    border-radius: 4px;
                    text-align: center;
                    color: #666;
                }
                .status-badge {
                    display: inline-block;
                    padding: 2px 8px;
                    border-radius: 3px;
                    font-size: 12px;
                    font-weight: 500;
                }
                .status-registered {
                    background: #d4edda;
                    color: #155724;
                }
                .status-pending-registration {
                    background: #fff3cd;
                    color: #856404;
                }
            </style>
        </div>
        <?php
    }

    public function add_assistant_column($columns) {
        $columns['assistant_count'] = 'Courses Assisted';
        return $columns;
    }

    public function display_assistant_column($column, $post_id) {
        if ($column === 'assistant_count') {
            global $wpdb;
            $confirmed_count = $wpdb->get_var($wpdb->prepare(
                "SELECT COUNT(*) FROM {$wpdb->prefix}lsim_assistant_history 
                WHERE instructor_id = %d",
                $post_id
            ));
            
            $email = get_post_meta($post_id, '_email', true);
            $pending_count = $wpdb->get_var($wpdb->prepare(
                "SELECT COUNT(*) FROM {$wpdb->prefix}lsim_pending_assistants 
                WHERE email = %s",
                $email
            ));
            
            echo esc_html($confirmed_count ?: '0');
            if ($pending_count > 0) {
                echo ' <span class="pending-count">(' . esc_html($pending_count) . ' pending)</span>';
            }
        }
    }

    public static function record_assistant($data) {
        global $wpdb;
        
        // Check if instructor exists
        $instructor = get_posts([
            'post_type' => 'instructor',
            'meta_key' => '_email',
            'meta_value' => $data['email'],
            'posts_per_page' => 1
        ]);

        if ($instructor) {
            return $wpdb->insert(
                $wpdb->prefix . 'lsim_assistant_history',
                [
                    'instructor_id' => $instructor[0]->ID,
                    'course_id' => $data['course_id'],
                    'created_at' => current_time('mysql')
                ],
                ['%d', '%d', '%s']
            );
        } else {
            return $wpdb->insert(
                $wpdb->prefix . 'lsim_pending_assistants',
                [
                    'course_id' => $data['course_id'],
                    'first_name' => $data['first_name'],
                    'last_name' => $data['last_name'],
                    'email' => $data['email'],
                    'created_at' => current_time('mysql')
                ],
                ['%d', '%s', '%s', '%s', '%s']
            );
        }
    }
}		