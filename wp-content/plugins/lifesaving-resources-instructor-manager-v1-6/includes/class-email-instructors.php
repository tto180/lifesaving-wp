<?php
if (!defined('ABSPATH')) exit;

class LSIM_Email_Instructors {
    const MAX_RECIPIENTS = 100;
    const MAX_CC = 10;
    const MAX_ATTACHMENT_SIZE = 10485760; // 10MB in bytes
    const ALLOWED_MIME_TYPES = [
        'application/pdf',
        'application/msword',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'application/vnd.ms-excel',
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        'text/plain',
        'image/jpeg',
        'image/png'
    ];

    public function __construct() {
        add_action('admin_enqueue_scripts', array($this, 'enqueue_assets'));
        add_action('wp_ajax_send_instructor_emails', array($this, 'handle_email_send'));
    }

    public function enqueue_assets($hook) {
        if ($hook !== 'lifesaving-resources_page_email-instructors') {
            return;
        }

        wp_enqueue_editor();
        wp_enqueue_media();

        wp_enqueue_style(
            'lsim-email-style',
            LSIM_PLUGIN_URL . 'assets/css/email-styles.css',
            array(),
            LSIM_VERSION
        );

        wp_enqueue_script(
            'lsim-email-script',
            LSIM_PLUGIN_URL . 'assets/js/email-scripts.js',
            array('jquery', 'wp-editor'),
            LSIM_VERSION,
            true
        );

        wp_localize_script('lsim-email-script', 'lsimEmailVars', array(
            'ajaxurl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('send_instructor_emails'),
            'maxCC' => self::MAX_CC,
            'maxAttachmentSize' => self::MAX_ATTACHMENT_SIZE,
            'allowedTypes' => array_map(function($mime) {
                return '.' . explode('/', $mime)[1];
            }, self::ALLOWED_MIME_TYPES)
        ));
    }

    public function render_email_page() {
        if (!current_user_can('manage_options')) {
            wp_die('Unauthorized access');
        }

        // Get counts for display
        $water_count = $this->get_instructor_count('water');
        $ice_count = $this->get_instructor_count('ice');
        ?>
        <div class="wrap">
            <h1>Email Instructors</h1>

            <div class="email-form-container">
                <form id="instructor-email-form" method="post">
                    <?php wp_nonce_field('send_instructor_emails', 'email_nonce'); ?>

                    <!-- Recipients Section -->
                    <div class="form-section">
                        <h2>Recipients</h2>
                        <div class="recipient-options">
                            <label>
                                <input type="checkbox" name="recipients[]" value="water" data-count="<?php echo esc_attr($water_count); ?>">
                                Water Rescue Instructors (<?php echo esc_html($water_count); ?> total)
                            </label>
                            <label>
                                <input type="checkbox" name="recipients[]" value="ice" data-count="<?php echo esc_attr($ice_count); ?>">
                                Ice Rescue Instructors (<?php echo esc_html($ice_count); ?> total)
                            </label>
                            <div class="active-only">
                                <label>
                                    <input type="checkbox" name="active_only" value="1" checked>
                                    Active instructors only
                                </label>
                            </div>
                        </div>
                        <div id="recipient-count" class="recipient-count"></div>
                    </div>

                    <!-- CC Section -->
                    <div class="form-section">
                        <h2>Additional Recipients</h2>
                        <div class="cc-field">
                            <label for="cc">CC (comma-separated emails, max <?php echo self::MAX_CC; ?>):</label>
                            <input type="text" id="cc" name="cc" class="regular-text">
                        </div>
                    </div>

                    <!-- Email Content Section -->
                    <div class="form-section">
                        <h2>Email Content</h2>
                        <div class="email-content">
                            <p>
                                <label for="email_subject">Subject:</label>
                                <input type="text" id="email_subject" name="subject" class="regular-text" required>
                            </p>

                            <div class="merge-tags">
                                <p>Available merge tags:</p>
                                <code>{instructor_name}</code>
                                <code>{certification_type}</code>
                                <code>{expiration_date}</code>
                            </div>

                            <?php 
                            wp_editor(
                                '', 
                                'email_content',
                                array(
                                    'textarea_name' => 'content',
                                    'media_buttons' => false,
                                    'textarea_rows' => 10,
                                    'teeny' => true
                                )
                            ); 
                            ?>
                        </div>
                    </div>

                    <!-- Attachments Section -->
                    <div class="form-section">
                        <h2>Attachments</h2>
                        <div class="attachment-area">
                            <button type="button" class="button" id="add-attachment">Add Attachment</button>
                            <div id="attachment-list"></div>
                            <p class="description">
                                Maximum total size: <?php echo size_format(self::MAX_ATTACHMENT_SIZE); ?><br>
                                Allowed file types: PDF, Word, Excel, Text, JPG, PNG
                            </p>
                        </div>
                    </div>

                    <!-- Preview and Send Buttons -->
                    <div class="form-actions">
                        <button type="button" class="button button-secondary" id="preview-email">Preview</button>
                        <button type="submit" class="button button-primary" id="send-email">Send Email</button>
                    </div>
                </form>
            </div>

            <!-- Preview Modal -->
            <div id="preview-modal" class="modal">
                <div class="modal-content">
                    <span class="close">&times;</span>
                    <h2>Email Preview</h2>
                    <div id="preview-content"></div>
                </div>
            </div>
        </div>
        <?php
    }

    private function get_instructor_count($type) {
        $args = array(
            'post_type' => 'instructor',
            'posts_per_page' => -1,
            'meta_query' => array(
                array(
                    'key' => "_{$type}_active",
                    'value' => '1'
                )
            )
        );
        return count(get_posts($args));
    }

    public function handle_email_send() {
        check_ajax_referer('send_instructor_emails', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_send_json_error(array('message' => 'Unauthorized access'));
            return;
        }

        // Validate form data
        $recipients = isset($_POST['recipients']) ? $_POST['recipients'] : array();
        if (empty($recipients)) {
            wp_send_json_error(array('message' => 'No recipient groups selected'));
            return;
        }

        // Validate CC emails
        $cc_emails = array();
        if (!empty($_POST['cc'])) {
            $cc_emails = array_map('trim', explode(',', $_POST['cc']));
            if (count($cc_emails) > self::MAX_CC) {
                wp_send_json_error(array('message' => 'Too many CC recipients'));
                return;
            }
            foreach ($cc_emails as $email) {
                if (!is_email($email)) {
                    wp_send_json_error(array('message' => "Invalid CC email: $email"));
                    return;
                }
            }
        }

        // Get instructor emails
        $instructor_emails = $this->get_instructor_emails($recipients, isset($_POST['active_only']));
        
        if (empty($instructor_emails)) {
            wp_send_json_error(array('message' => 'No matching instructors found'));
            return;
        }

        // Process attachments
        $attachments = array();
        if (!empty($_FILES['attachments'])) {
            $attachments = $this->process_attachments($_FILES['attachments']);
            if (is_wp_error($attachments)) {
                wp_send_json_error(array('message' => $attachments->get_error_message()));
                return;
            }
        }

        // Send emails in batches
        $subject = sanitize_text_field($_POST['subject']);
        $content = wp_kses_post($_POST['content']);
        
        $batches = array_chunk($instructor_emails, self::MAX_RECIPIENTS);
        $total_sent = 0;
        $errors = array();

        foreach ($batches as $batch) {
            $result = $this->send_email_batch($batch, $cc_emails, $subject, $content, $attachments);
            if (is_wp_error($result)) {
                $errors[] = $result->get_error_message();
            } else {
                $total_sent += count($batch);
            }
            
            // Add a small delay between batches
            if (count($batches) > 1) {
                sleep(5);
            }
        }

        // Clean up temporary attachment files
        $this->cleanup_attachments($attachments);

        if (!empty($errors)) {
            wp_send_json_error(array(
                'message' => 'Some emails failed to send. ' . implode(' ', $errors)
            ));
            return;
        }

        wp_send_json_success(array(
            'message' => sprintf('Successfully sent emails to %d instructors.', $total_sent)
        ));
    }

    private function get_instructor_emails($types, $active_only = true) {
        $emails = array();
        
        foreach ($types as $type) {
            $args = array(
                'post_type' => 'instructor',
                'posts_per_page' => -1,
                'meta_query' => array(
                    'relation' => 'AND',
                    array(
                        'key' => '_email',
                        'compare' => 'EXISTS'
                    )
                )
            );

            if ($active_only) {
                $args['meta_query'][] = array(
                    'key' => "_{$type}_active",
                    'value' => '1'
                );
            }

            $instructors = get_posts($args);
            foreach ($instructors as $instructor) {
                $email = get_post_meta($instructor->ID, '_email', true);
                if ($email && is_email($email)) {
                    $emails[$instructor->ID] = array(
                        'email' => $email,
                        'name' => $instructor->post_title,
                        'type' => $type,
                        'expiration' => $this->get_certification_expiration($instructor->ID, $type)
                    );
                }
            }
        }

        return $emails;
    }

    private function process_attachments($files) {
        if (!function_exists('wp_handle_upload')) {
            require_once(ABSPATH . 'wp-admin/includes/file.php');
        }

        $attachments = array();
        $total_size = 0;

        foreach ($files['name'] as $key => $value) {
            if ($files['error'][$key] === UPLOAD_ERR_OK) {
                $total_size += $files['size'][$key];
                
                if ($total_size > self::MAX_ATTACHMENT_SIZE) {
                    return new WP_Error('attachment_size', 'Total attachment size exceeds limit');
                }

                $file = array(
                    'name'     => $files['name'][$key],
                    'type'     => $files['type'][$key],
                    'tmp_name' => $files['tmp_name'][$key],
                    'error'    => $files['error'][$key],
                    'size'     => $files['size'][$key]
                );

                if (!in_array($file['type'], self::ALLOWED_MIME_TYPES)) {
                    return new WP_Error('file_type', 'Invalid file type: ' . $file['name']);
                }

                $upload = wp_handle_upload($file, array('test_form' => false));

                if (isset($upload['error'])) {
                    return new WP_Error('upload_error', $upload['error']);
                }

                $attachments[] = $upload['file'];
            }
        }

        return $attachments;
    }

    private function send_email_batch($instructors, $cc_emails, $subject, $content, $attachments) {
        $headers = array('Content-Type: text/html; charset=UTF-8');
        
        if (!empty($cc_emails)) {
            foreach ($cc_emails as $cc) {
                $headers[] = 'Cc: ' . $cc;
            }
        }

        foreach ($instructors as $instructor_id => $data) {
            // Process merge tags
            $personalized_content = str_replace(
                array('{instructor_name}', '{certification_type}', '{expiration_date}'),
                array(
                    $data['name'],
                    ucfirst($data['type']) . ' Rescue',
                    date('F j, Y', strtotime($data['expiration']))
                ),
                $content
            );

            $success = wp_mail(
                $data['email'],
                $subject,
                $personalized_content,
                $headers,
                $attachments
            );

            if (!$success) {
                return new WP_Error(
                    'send_failed', 
                    sprintf('Failed to send email to %s', $data['email'])
                );
            }
        }

        return true;
    }

    private function cleanup_attachments($attachments) {
        foreach ($attachments as $file) {
            if (file_exists($file)) {
                @unlink($file);
            }
        }
    }

    private function get_certification_expiration($instructor_id, $type) {
        // Get most recent certification date
        $dates = array();
        
        $original_date = get_post_meta($instructor_id, "_{$type}_original_date", true);
        if ($original_date) {
            $dates[] = $original_date;
        }
        
        $recert_dates = get_post_meta($instructor_id, "_{$type}_recert_dates", true) ?: array();
        $dates = array_merge($dates, $recert_dates);
        
        if (empty($dates)) {
            return null;
        }
        
        sort($dates);
        $most_recent = end($dates);
        
        // Calculate expiration (December 31st, 3 years after certification year)
		$cert_year = date('Y', strtotime($most_recent));
        return date('Y-12-31', strtotime($cert_year . ' +3 years'));
    }
}