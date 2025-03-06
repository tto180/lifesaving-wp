<?php
if(class_exists('BridgeQodeWidget')) {
    class BridgeQodeSeparator extends BridgeQodeWidget 	{
        protected $params;

        public function __construct() {
            parent::__construct(
                'qode_separator',
                esc_html__('Qode Separator', 'bridge'),
                array('description' => esc_html__('Display Qode Separator', 'bridge'),)
            );

            $this->setParams();
        }

        protected function setParams() {
            $this->params = array(
                array(
                    'name' => 'thickness',
                    'type' => 'textfield',
                    'title' => esc_html__('Thickness (px)', 'bridge'),
                ),
                array(
                    'name' => 'disable_below',
                    'type' => 'dropdown',
                    'title' => esc_html__('Disable separator below', 'bridge'),
                    'options' => array(
						'' => esc_html__( 'Never', 'bridge' ),
						'1024' => esc_html__( '1024px', 'bridge' ),
						'768' => esc_html__( '768px', 'bridge' ),
						'600' => esc_html__( '600px', 'bridge' ),
						'480' => esc_html__( '480px', 'bridge' ),
                    )
                ),
            );
        }

        public function widget($args, $instance) {
            extract($args);

            $thickness = 0;

            if( ! empty( $instance['thickness'] ) ){
                $thickness = bridge_qode_filter_px( $instance['thickness'] );
            }
			
			$separator_classes = array(
				'widget',
				'qode_separator_widget'
			);
	        
	        $separator_classes[] = ! empty( $instance['disable_below'] ) ? 'qode-disabled-below--' . $instance['disable_below'] : '';

            echo '<div class="' . implode( ' ', $separator_classes ) . '" style="margin-bottom: ' . $thickness . 'px;">';

            echo '</div>';
        }
    }
}