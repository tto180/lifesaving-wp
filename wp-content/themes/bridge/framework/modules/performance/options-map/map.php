<?php

if(!function_exists('bridge_qode_performance_options_map')) {
    /**
     * Performance options page
     */
    function bridge_qode_performance_options_map() {
		
		bridge_qode_add_admin_page(array(
            'slug' => '_performance',
            'title' => esc_html__('Performance', 'bridge'),
            'icon' => 'fa fa-cog'
        ));

        $panel_general = bridge_qode_add_admin_panel(array(
            'title'	=> esc_html__('General', 'bridge'),
            'name'	=> 'panel_general',
            'page'	=> '_performance'
        ));
		
	    
	    $qode_ui_scripts = bridge_qode_return_ui_scripts_array();
		
		//Make tabs and accordion scripts enabled by default
		$qode_default_scripts = array(
			'jquery-ui-tabs',
			'jquery-ui-accordion'
		);
		
		if( is_array( $qode_ui_scripts ) && count( $qode_ui_scripts ) > 0 ) {
			bridge_qode_add_admin_field(
				array(
					'name'          => 'qode_ui_scripts_option',
					'type'          => 'checkboxgroup',
					'default_value' => $qode_default_scripts,
					'label'         => esc_html__( 'JQuery Scripts ', 'bridge' ),
					'description'   => esc_html__( 'Choose which JQuery scripts will be loaded for your site. By default, all scripts are loaded initially', 'bridge' ),
					'parent'        => $panel_general,
					'options'       => $qode_ui_scripts
				)
			);
		}
	    
		
	    $icon_collections = bridge_qode_get_icon_packs();
		
		if( is_array( $icon_collections ) && count( $icon_collections ) > 0 ) {
			//remove font awesome as option to disable since it is default icon pack used by theme
			if( array_key_exists('font_awesome', $icon_collections) ){
				unset($icon_collections['font_awesome']);
			}
			
			$icon_collections_keys = array_keys( $icon_collections );
			
			bridge_qode_add_admin_field(
				array(
					'parent' => $panel_general,
					'type' => 'checkboxgroup',
					'name' => 'qode_performance_icon_packs',
					'label' => esc_html__('Icon Packs', 'bridge'),
					'description' => esc_html__('Choose which icon pack will be loaded for your site. By default, all icon packs are loaded initially','bridge'),
					'options' => $icon_collections,
					'default_value' => $icon_collections_keys
				)
			);
		}
	    
	    bridge_qode_add_admin_field(
		    array(
			    'parent'        => $panel_general,
			    'type'          => 'yesno',
			    'name'          => 'google_fonts_display_swap_enabled',
			    'default_value' => 'no',
			    'label'         => esc_html__('Enable Display Swap for Google Fonts', 'bridge'),
			    'description'   => esc_html__('Enabling this option will force swap display for all google fonts', 'bridge'),
		    )
	    );
	    
	    $panel_loading_type = bridge_qode_add_admin_panel(
		    array(
			    'title'	=> esc_html__('Third Party Scripts Loading Type', 'bridge'),
			    'name'	=> 'panel_loading_type',
			    'page'	=> '_performance'
	        )
	    );
		
	    bridge_qode_add_admin_section_subtitle(
			array(
	            'name' => 'third_party_scripts_loading_type_title',
	            'title' => esc_html__('Choose loading type of the third party scripts. Default is in footer. Async allows your script to run as soon as it\'s loaded, without blocking other elements on the page. Defer means your script will only execute after the page has finished loading.', 'bridge-core'),
	            'parent' => $panel_loading_type
	        )
	    );
	    
	    $third_party_scripts = bridge_qode_get_third_party_scripts_array();
	    
	    if( ! empty( $third_party_scripts['register'] ) && is_array( $third_party_scripts['register'] ) && count( $third_party_scripts['register'] ) > 0 ) {
		    foreach( $third_party_scripts['register'] as $handle => $script ) {
			    if( $script ) {
				    ${$handle . '_group'} = bridge_qode_add_admin_group(
					    array(
						    'name'          => $handle . '_group',
						    'title'         => '\'' . $handle . esc_html__('\' Script Options', 'bridge'),
						    'description'   => esc_html__('Set loading options for this script', 'bridge'),
						    'parent'        => $panel_loading_type
					    )
				    );
				    
				    ${$handle . '_row'} = bridge_qode_add_admin_row(
					    array(
						    'name' => $handle . '_row',
						    'next' => true,
						    'parent' => ${$handle . '_group'}
					    )
				    );
				    
				    bridge_qode_add_admin_field(
					    array(
						    'parent'        => ${$handle . '_row'},
						    'type'          => 'selectsimple',
						    'name'          => 'script_' . $handle . '_loading_type',
						    'default_value' => '',
						    'label'         => esc_html__('Script loading type', 'bridge'),
						    'description'   => '',
						    'options'       => array(
							    '' => esc_html__( 'Default', 'bridge' ),
							    'defer' => esc_html__( 'Defer', 'bridge' ),
							    'async' => esc_html__( 'Async', 'bridge' ),
						    )
					    
					    )
				    );
				    
				    bridge_qode_add_admin_field(
					    array(
						    'parent'        => ${$handle . '_row'},
						    'type'          => 'yesnosimple',
						    'name'          => 'script_' . $handle . '_force_load',
						    'default_value' => 'no',
						    'label'         => esc_html__('Force Load', 'bridge'),
						    'description'   => '',
					    
					    )
				    );
			    }
		    }
	    }
	    
	    $google_maps_group = bridge_qode_add_admin_group(
		    array(
			    'name'          => 'google_maps_group',
			    'title'         => esc_html__('\'google-maps\' Script Options', 'bridge'),
			    'description'   => esc_html__('Set loading options for this script', 'bridge'),
			    'parent'        => $panel_loading_type
		    )
	    );
	    
	    $google_maps_row = bridge_qode_add_admin_row(
		    array(
			    'name' => 'google_maps_row',
			    'next' => true,
			    'parent' => $google_maps_group
		    )
	    );
	    
	    bridge_qode_add_admin_field(
		    array(
			    'parent'        => $google_maps_row,
			    'type'          => 'selectsimple',
			    'name'          => 'script_google_maps_loading_type',
			    'default_value' => '',
			    'label'         => esc_html__('Script loading type', 'bridge'),
			    'description'   => '',
			    'options'       => array(
				    '' => esc_html__( 'Default', 'bridge' ),
				    'defer' => esc_html__( 'Defer', 'bridge' ),
				    'async' => esc_html__( 'Async', 'bridge' ),
			    )
		    
		    )
	    );
	    
	    bridge_qode_add_admin_field(
		    array(
			    'parent'        => $google_maps_row,
			    'type'          => 'yesnosimple',
			    'name'          => 'script_google_maps_force_load',
			    'default_value' => 'no',
			    'label'         => esc_html__('Force Load', 'bridge'),
			    'description'   => '',
		    
		    )
	    );
		
		if( ! empty( $third_party_scripts['enqueue'] ) && is_array( $third_party_scripts['enqueue'] ) && count( $third_party_scripts['enqueue'] ) > 0 ) {
			foreach( $third_party_scripts['enqueue'] as $handle => $script ) {
				if( $script ) {
					${$handle . '_group'} = bridge_qode_add_admin_group(
						array(
	                        'name'          => $handle . '_group',
	                        'title'         => '\'' . $handle . esc_html__('\' Script Options', 'bridge'),
	                        'description'   => esc_html__('Set loading options for this script', 'bridge'),
	                        'parent'        => $panel_loading_type
	                    )
					);
					
					${$handle . '_row'} = bridge_qode_add_admin_row(
						array(
                            'name' => $handle . '_row',
                            'next' => true,
                            'parent' => ${$handle . '_group'}
                        )
					);
					
					bridge_qode_add_admin_field(
						array(
                            'parent'        => ${$handle . '_row'},
                            'type'          => 'selectsimple',
                            'name'          => 'script_' . $handle . '_loading_type',
                            'default_value' => '',
                            'label'         => esc_html__('Script loading type', 'bridge'),
                            'description'   => '',
                            'options'       => array(
	                            '' => esc_html__( 'Default', 'bridge' ),
	                            'defer' => esc_html__( 'Defer', 'bridge' ),
	                            'async' => esc_html__( 'Async', 'bridge' ),
                            )
                            
                        )
					);
				}
			}
		}
    }
	
    add_action('bridge_qode_action_options_map', 'bridge_qode_performance_options_map', 190);
}