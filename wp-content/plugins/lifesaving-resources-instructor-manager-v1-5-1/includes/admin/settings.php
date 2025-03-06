<?php
class LSIM_Admin_Settings {
    private $settings_tabs;

    public function __construct() {
        $this->settings_tabs = [
            'general' => 'General Settings',
            'forms' => 'Form Configuration',
            'form_fields' => 'Form Fields',
            'notifications' => 'Notification Settings'
        ];

        add_action('admin_init', [$this, 'register_settings']);
    }

    public function register_settings() {
        // Register form settings
        register_setting(
            'lsim_settings',
            'lsim_form_ids',
            [
                'type' => 'array',
                'default' => ['ice' => 3, 'water' => 1],
                'sanitize_callback' => function($value) {
                    if (!is_array($value)) {
                        return ['ice' => 3, 'water' => 1];
                    }
                    return [
                        'ice' => isset($value['ice']) ? intval($value['ice']) : 3,
                        'water' => isset($value['water']) ? intval($value['water']) : 1
                    ];
                }
            ]
        );

        // Add forms section
        add_settings_section(
            'lsim_form_settings',
            'Gravity Forms Configuration',
            [$this, 'render_form_settings_description'],
            'lsim_form_settings'
        );

        // Add form ID fields
        add_settings_field(
            'lsim_ice_form_id',
            'Ice Rescue Form ID',
            [$this, 'render_form_id_field'],
            'lsim_form_settings',
            'lsim_form_settings',
            ['form_type' => 'ice']
        );

        add_settings_field(
            'lsim_water_form_id',
            'Water Rescue Form ID',
            [$this, 'render_form_id_field'],
            'lsim_form_settings',
            'lsim_form_settings',
            ['form_type' => 'water']
        );
        
        // Add form field mappings settings
        register_setting(
            'lsim_settings',
            'lsim_form_field_mappings',
            [
                'type' => 'array',
                'default' => [
                    'ice' => [
                        'first_name' => '3.3',
                        'last_name' => '3.6',
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
                        'assistant_list' => '28'  // New single field for assistants list
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
                        'hours' => '39',
                        'participants_awareness' => '15',
                        'participants_technician' => '16',
                        'participants_operations' => '17',
                        'participants_surf_swiftwater' => '18',
                        'assistant_list' => '28'  // New single field for assistants list
                    ]
                ],
                'sanitize_callback' => function($value) {
                    if (!is_array($value)) {
                        return $this->get_default_field_mappings();
                    }
                    foreach (['ice', 'water'] as $type) {
                        if (!isset($value[$type]) || !is_array($value[$type])) {
                            $value[$type] = $this->get_default_field_mappings()[$type];
                        }
                    }
                    return $value;
                }
            ]
        );
    }

    private function get_default_field_mappings() {
        return [
            'ice' => [
                'first_name' => '3.3',
                'last_name' => '3.6',
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
                'first_name' => '3.3',
                'last_name' => '3.6',
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
    }

    public function render_settings_page() {
        if (!current_user_can('manage_options')) {
            wp_die('Unauthorized access');
        }

        $active_tab = isset($_GET['tab']) ? sanitize_text_field($_GET['tab']) : 'general';
        ?>
        <div class="wrap">
            <h1>Lifesaving Resources Settings</h1>
            
            <h2 class="nav-tab-wrapper">
                <?php foreach ($this->settings_tabs as $tab => $name): ?>
                    <a href="?page=instructor-settings&tab=<?php echo esc_attr($tab); ?>" 
                       class="nav-tab <?php echo $active_tab === $tab ? 'nav-tab-active' : ''; ?>">
                        <?php echo esc_html($name); ?>
                    </a>
                <?php endforeach; ?>
            </h2>

            <form action="options.php" method="post">
                <?php
                settings_fields('lsim_settings');
                
                if ($active_tab === 'forms') {
                    do_settings_sections('lsim_form_settings');
                } elseif ($active_tab === 'notifications') {
                    do_settings_sections('lsim_notification_settings');
                } elseif ($active_tab === 'form_fields') {
                    $this->render_form_fields_tab();
                } elseif ($active_tab === 'unrecognized') {
                    do_action('lsim_settings_unrecognized');
                } else {
                    do_settings_sections('lsim_general_settings');
                }
                
                submit_button();
                ?>
            </form>
        </div>
        <?php
    }
public function render_form_settings_description() {
        ?>
        <p>Configure the Gravity Forms IDs used for course submissions. These forms must be properly configured with the expected field IDs for the plugin to function correctly.</p>
        <?php
    }

    public function render_form_id_field($args) {
        $saved_form_ids = get_option('lsim_form_ids');
        $form_ids = is_array($saved_form_ids) ? $saved_form_ids : ['ice' => 3, 'water' => 1];
        
        $form_type = $args['form_type'];
        $current_value = $form_ids[$form_type] ?? ($form_type === 'ice' ? 3 : 1);
        
        ?>
        <input type="number" 
               name="lsim_form_ids[<?php echo esc_attr($form_type); ?>]" 
               value="<?php echo esc_attr($current_value); ?>" 
               class="small-text">
        <p class="description">
            <?php echo esc_html(ucfirst($form_type)) ?> Rescue course completion form ID in Gravity Forms
        </p>
        <?php
    }

    public function render_form_fields_tab() {
        $field_mappings = get_option('lsim_form_field_mappings', []);
        $form_ids = get_option('lsim_form_ids', ['ice' => 3, 'water' => 1]);
        ?>
        <div class="form-fields-settings">
            <h2>Form Field Configuration</h2>
            <p>Configure the field IDs for each form type. Use the verify button to check if fields exist in the form.</p>

            <div class="nav-tab-wrapper">
                <a href="#ice-fields" class="nav-tab nav-tab-active">Ice Rescue Fields</a>
                <a href="#water-fields" class="nav-tab">Water Rescue Fields</a>
            </div>

            <?php foreach (['ice', 'water'] as $type): ?>
            <div id="<?php echo $type; ?>-fields" class="field-mapping-section" <?php echo $type === 'ice' ? '' : 'style="display:none;"'; ?>>
                <h3><?php echo ucfirst($type); ?> Rescue Form Fields</h3>
                
                <h4>Instructor Information</h4>
                <table class="form-table">
                    <tr>
                        <th>First Name Field ID</th>
                        <td>
                            <input type="text" name="lsim_form_field_mappings[<?php echo $type; ?>][first_name]" 
                                   value="<?php echo esc_attr($field_mappings[$type]['first_name'] ?? '3.3'); ?>"
                                   class="small-text">
                            <p class="description">The first name sub-field from the Name field (e.g., 3.3)</p>
                        </td>
                    </tr>
                    <tr>
                        <th>Last Name Field ID</th>
                        <td>
                            <input type="text" name="lsim_form_field_mappings[<?php echo $type; ?>][last_name]" 
                                   value="<?php echo esc_attr($field_mappings[$type]['last_name'] ?? '3.6'); ?>"
                                   class="small-text">
                            <p class="description">The last name sub-field from the Name field (e.g., 3.6)</p>
                        </td>
                    </tr>
                    <tr>
                        <th>Department Field ID</th>
                        <td>
                            <input type="text" name="lsim_form_field_mappings[<?php echo $type; ?>][department]" 
                                   value="<?php echo esc_attr($field_mappings[$type]['department'] ?? '5'); ?>"
                                   class="small-text">
                        </td>
                    </tr>
                    <tr>
                        <th>Address Field ID</th>
                        <td>
                            <input type="text" name="lsim_form_field_mappings[<?php echo $type; ?>][address]" 
                                   value="<?php echo esc_attr($field_mappings[$type]['address'] ?? '6'); ?>"
                                   class="small-text">
                        </td>
                    </tr>
                    <tr>
                        <th>Phone Field ID</th>
                        <td>
                            <input type="text" name="lsim_form_field_mappings[<?php echo $type; ?>][phone]" 
                                   value="<?php echo esc_attr($field_mappings[$type]['phone'] ?? '7'); ?>"
                                   class="small-text">
                        </td>
                    </tr>
                    <tr>
                        <th>Email Field ID</th>
                        <td>
                            <input type="text" name="lsim_form_field_mappings[<?php echo $type; ?>][email]" 
                                   value="<?php echo esc_attr($field_mappings[$type]['email'] ?? '8'); ?>"
                                   class="small-text">
                        </td>
                    </tr>
                </table>

                <h4>Course Information</h4>
                <table class="form-table">
                    <tr>
                        <th>Course Date Field ID</th>
                        <td>
                            <input type="text" name="lsim_form_field_mappings[<?php echo $type; ?>][course_date]" 
                                   value="<?php echo esc_attr($field_mappings[$type]['course_date'] ?? '12'); ?>"
                                   class="small-text">
                        </td>
                    </tr>
                    <tr>
                        <th>Location Field ID</th>
                        <td>
                            <input type="text" name="lsim_form_field_mappings[<?php echo $type; ?>][location]" 
                                   value="<?php echo esc_attr($field_mappings[$type]['location'] ?? '13'); ?>"
                                   class="small-text">
                        </td>
                    </tr>
                    <tr>
                        <th>Hours Field ID</th>
                        <td>
                            <input type="text" name="lsim_form_field_mappings[<?php echo $type; ?>][hours]" 
                                   value="<?php echo esc_attr($field_mappings[$type]['hours'] ?? ($type === 'ice' ? '40' : '39')); ?>"
                                   class="small-text">
                        </td>
                    </tr>
                </table>

                <h4>Participant Counts</h4>
                <table class="form-table">
                    <tr>
                        <th>Awareness Count Field ID</th>
                        <td>
                            <input type="text" name="lsim_form_field_mappings[<?php echo $type; ?>][participants_awareness]" 
                                   value="<?php echo esc_attr($field_mappings[$type]['participants_awareness'] ?? '15'); ?>"
                                   class="small-text">
                        </td>
                    </tr>
                    <tr>
                        <th>Technician Count Field ID</th>
                        <td>
                            <input type="text" name="lsim_form_field_mappings[<?php echo $type; ?>][participants_technician]" 
                                   value="<?php echo esc_attr($field_mappings[$type]['participants_technician'] ?? '16'); ?>"
                                   class="small-text">
                        </td>
                    </tr>
                    <tr>
                        <th>Operations Count Field ID</th>
                        <td>
                            <input type="text" name="lsim_form_field_mappings[<?php echo $type; ?>][participants_operations]" 
                                   value="<?php echo esc_attr($field_mappings[$type]['participants_operations'] ?? '17'); ?>"
                                   class="small-text">
                        </td>
                    </tr>
                    <?php if ($type === 'water'): ?>
                    <tr>
                        <th>Surf/Swiftwater Count Field ID</th>
                        <td>
                            <input type="text" name="lsim_form_field_mappings[<?php echo $type; ?>][participants_surf_swiftwater]" 
                                   value="<?php echo esc_attr($field_mappings[$type]['participants_surf_swiftwater'] ?? '18'); ?>"
                                   class="small-text">
                        </td>
                    </tr>
                    <?php endif; ?>
                </table>

                <h4>Assistant Information</h4>
                <table class="form-table">
                    <tr>
                        <th>Assistant List Field ID</th>
                        <td>
                            <input type="text" 
                                   name="lsim_form_field_mappings[<?php echo $type; ?>][assistant_list]" 
                                   value="<?php echo esc_attr($field_mappings[$type]['assistant_list'] ?? '28'); ?>"
                                   class="small-text">
                            <p class="description">ID of the list field containing assistant information (first name, last name, and email)</p>
                        </td>
                    </tr>
                </table>

                <p class="submit">
                    <button type="button" class="button verify-fields" data-form-type="<?php echo $type; ?>" 
                            data-form-id="<?php echo esc_attr($form_ids[$type]); ?>">
                        Verify Fields
                    </button>
                </p>
            </div>
            <?php endforeach; ?>
        </div>

        <style>
            .form-fields-settings .form-table {
                margin-bottom: 2em;
            }
            .form-fields-settings h4 {
                margin: 1.5em 0 1em;
                padding-bottom: 0.5em;
                border-bottom: 1px solid #ccc;
            }
            .form-fields-settings .description {
                margin-top: 5px;
            }
            .verify-fields {
                margin-top: 1em;
            }
        </style>

        <script>
        jQuery(document).ready(function($) {
            // Tab switching
            $('.nav-tab-wrapper .nav-tab').on('click', function(e) {
                e.preventDefault();
                const target = $(this).attr('href');
                
                $('.nav-tab').removeClass('nav-tab-active');
                $(this).addClass('nav-tab-active');
                
                $('.field-mapping-section').hide();
                $(target).show();
            });

            // Field verification
            $('.verify-fields').on('click', function() {
                const button = $(this);
                const formType = button.data('form-type');
                const formId = button.data('form-id');
                
                button.prop('disabled', true)
                      .html('<span class="dashicons dashicons-update spin"></span> Verifying...');
                
                $.post(ajaxurl, {
                    action: 'verify_form_fields',
                    nonce: '<?php echo wp_create_nonce('verify_form_fields'); ?>',
                    form_type: formType,
                    form_id: formId,
                    fields: $(`#${formType}-fields input`).serialize()
                }, function(response) {
                    if (response.success) {
                        alert('All fields verified successfully!');
                    } else {
                        alert(response.data.message || 'Error verifying fields');
                    }
                }).fail(function() {
                    alert('Network error while verifying fields. Please try again.');
                }).always(function() {
                    button.prop('disabled', false).text('Verify Fields');
                });
            });
        });
        </script>
        <?php
    }
}