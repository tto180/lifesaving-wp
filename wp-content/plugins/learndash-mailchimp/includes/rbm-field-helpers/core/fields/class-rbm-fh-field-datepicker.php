<?php
/**
 * Field: Date Picker
 *
 * @since 1.1.0
 *
 * @package RBMFieldHelpers
 */

defined( 'ABSPATH' ) || die();

/**
 * Class RBM_FH_Field_DatePicker
 *
 * @since 1.1.0
 */
class RBM_FH_Field_DatePicker extends RBM_FH_Field {

	/**
	 * Field defaults.
	 *
	 * @since 1.1.0
	 *
	 * @var array
	 */
	public $defaults = array(
		'default'         => '',
		'format'          => '',
		'datepicker_args' => array(
			'altInput'         => true,
			'dateFormat'       => 'Ymd', // Saved format
			'altFormat'        => 'F j, Y', // Display format
		),
	);

	/**
	 * RBM_FH_Field_DatePicker constructor.
	 *
	 * @since 1.1.0
	 *
	 * @var string $name
	 * @var array $args
	 * @var mixed $value
	 */
	function __construct( $name, $args = array() ) {

		// Cannot use function in property declaration
		$this->defaults['format'] = get_option( 'date_format', 'F j, Y' );
		
		// Ensure the Date/Time Format matches the stored format in WordPress
		$this->defaults['datepicker_args']['altFormat'] = RBM_FH_Field_DateTimePicker::php_date_to_flatpickr( $this->defaults['format'] );
		
		if ( ! isset( $args['datepicker_args'] ) ) {
			$args['datepicker_args'] = array();
		}

		// Default options
		$args['datepicker_args'] = wp_parse_args( $args['datepicker_args'], $this->defaults['datepicker_args'] );

		if ( ! isset( $args['default'] ) ) {

			// This is used when creating the field HTML
			$args['default'] = current_time( $args['datepicker_args']['dateFormat'] );

		}

		parent::__construct( $name, $args );
	}

	/**
	 * Outputs the field.
	 *
	 * @since 1.1.0
	 *
	 * @param string $name Name of the field.
	 * @param mixed $value Value of the field.
	 * @param array $args Field arguments.
	 */
	public static function field( $name, $value, $args = array() ) {

		// Datepicker args
		if ( $args['datepicker_args'] ) {

			add_filter( 'rbm_field_helpers_admin_data', function ( $data ) use ( $args, $name ) {

				$data["datepicker_args_$name"] = $args['datepicker_args'];

				return $data;
			} );
		}

		do_action( "{$args['prefix']}_fieldhelpers_do_field", 'datepicker', $args, $name, $value );
	}
}