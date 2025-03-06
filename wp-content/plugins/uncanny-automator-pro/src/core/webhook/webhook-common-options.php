<?php

namespace Uncanny_Automator_Pro;

use WP_REST_Request;

/**
 * Webhook_Common_Options
 */
class Webhook_Common_Options {
	/**
	 * Store data types
	 *
	 * @var array
	 */
	private static $data_types = array();
	/**
	 * Store data type keys
	 *
	 * @var array
	 */
	private static $data_types_key_store = array();

	/**
	 * Webhook_Common_Options
	 */
	public function __construct() {
		self::set_webhook_data_types( 'text', __( 'Text', 'uncanny-automator-pro' ) );
		self::set_webhook_data_types( 'email', __( 'Email', 'uncanny-automator-pro' ) );
		self::set_webhook_data_types( 'int', __( 'Integer', 'uncanny-automator-pro' ) );
		self::set_webhook_data_types( 'float', __( 'Float', 'uncanny-automator-pro' ) );
		self::set_webhook_data_types( 'url', __( 'URL', 'uncanny-automator-pro' ) );
		/**
		 * Token parsing
		 */
		add_filter( 'automator_maybe_parse_token', array( __CLASS__, 'parse_webhook_tokens' ), 200, 6 );

		$hooks = array(
			'automator_maybe_trigger_webhooks_tokens',
			'automator_maybe_trigger_google_sheets_tokens',
			'automator_maybe_trigger_ifttt_tokens',
			'automator_maybe_trigger_integrately_tokens',
			'automator_maybe_trigger_integromat_tokens',
			'automator_maybe_trigger_konnectz_it_tokens',
			'automator_maybe_trigger_optinmonster_tokens',
			'automator_maybe_trigger_make_tokens',
			'automator_maybe_trigger_thrivecart_tokens',
			'automator_maybe_trigger_typeform_tokens',
			'automator_maybe_trigger_zapier_tokens',
		);

		foreach ( $hooks as $hook ) {
			add_filter(
				$hook,
				array( $this, 'webhooks_possible_tokens' ),
				20,
				2
			);
		}
	}

	/**
	 * Get webhook data types
	 *
	 * @return array
	 */
	public static function get_webhook_data_types() {
		return apply_filters( 'automator_pro_webhook_data_types', self::$data_types );
	}

	/**
	 * Setting webhook data types
	 *
	 * @param $data_type
	 * @param $label
	 *
	 * @return void
	 */
	public static function set_webhook_data_types( $data_type, $label ) {
		if ( ! array_key_exists( $data_type, self::$data_types_key_store ) ) {
			self::$data_types[] = array(
				'value' => $data_type,
				'text'  => $label,
			);
		}
		self::$data_types_key_store[ $data_type ] = $label;
	}

	/**
	 * Return 'options_group' in trigger definition
	 *
	 * @return array
	 */
	public static function get_webhook_options_group( $options = array(), $formats = array() ) {
		$defaults = array(
			'url'                 => true,
			'format'              => true,
			'headers'             => true,
			'fields'              => true,
			'url_description'     => esc_attr__( 'Send your webhook to this URL. Supports PUT, GET and POST methods.', 'uncanny-automator-pro' ),
			'format_description'  => esc_attr__( 'Select the incoming data format sent by the webhook source. If you are unsure, leave this value unchanged unless you are experiencing issues.', 'uncanny-automator-pro' ),
			'headers_description' => esc_attr__( 'Any headers defined here must match the headers on the incoming request or the request will be ignored.', 'uncanny-automator-pro' ),
			'fields_description'  => sprintf( esc_attr__( 'Manually specify the data that will be received or click the "%1$s" button to listen for a sample webhook.', 'uncanny-automator-pro' ), __( 'Get samples', 'uncanny-automator-pro' ) ),
			'url_label'           => esc_attr__( 'Webhook URL', 'uncanny-automator-pro' ),
			'format_label'        => esc_attr__( 'Data format', 'uncanny-automator-pro' ),
			'headers_label'       => esc_attr__( 'Security headers', 'uncanny-automator-pro' ),
			'fields_label'        => esc_attr__( 'Fields', 'uncanny-automator-pro' ),
		);
		if ( empty( $formats ) ) {
			$formats = array(
				'json'                  => 'JSON',
				'x-www-form-urlencoded' => 'x-www-form-urlencoded',
				'auto'                  => __( 'Auto', 'uncanny-automator-pro' ),
				'xml'                   => 'XML',
				'form-data'             => 'form-data',
			);
		}
		$options        = wp_parse_args( $options, $defaults );
		$webhook_fields = array();
		if ( $options['url'] ) {
			$webhook_fields[] = array(
				'input_type'        => 'text',
				'option_code'       => 'WEBHOOK_URL',
				'label'             => $options['url_label'],
				'description'       => $options['url_description'],
				'required'          => true,
				'read_only'         => true,
				'copy_to_clipboard' => true,
				'default_value'     => '',
				'is_ajax'           => true,
				'endpoint'          => 'webhook_url_get_webhook_url',
			);
		}
		// Action event
		if ( $options['format'] ) {
			$webhook_fields[] = array(
				'input_type'    => 'select',
				'option_code'   => 'DATA_FORMAT',
				/* translators: HTTP request method */
				'label'         => $options['format_label'],
				'description'   => $options['format_description'],
				'required'      => true,
				'default_value' => 'auto',
				'options'       => apply_filters(
					'automator_pro_incoming_webhook_content_types',
					$formats
				),
			);
		}
		// Header
		if ( $options['headers'] ) {
			$webhook_fields[] = array(
				'input_type'        => 'repeater',
				'option_code'       => 'WEBHOOK_HEADERS',
				'label'             => $options['headers_label'],
				'description'       => $options['headers_description'],
				'required'          => false,
				'fields'            => array(
					array(
						'input_type'      => 'text',
						'option_code'     => 'NAME',
						'label'           => esc_attr__( 'Name', 'uncanny-automator-pro' ),
						'supports_tokens' => true,
						'required'        => true,
					),
					array(
						'input_type'      => 'text',
						'option_code'     => 'VALUE',
						'label'           => esc_attr__( 'Value', 'uncanny-automator-pro' ),
						'supports_tokens' => true,
						'required'        => true,
					),
				),

				/* translators: Non-personal infinitive verb */
				'add_row_button'    => esc_attr__( 'Add header', 'uncanny-automator-pro' ),
				/* translators: Non-personal infinitive verb */
				'remove_row_button' => esc_attr__( 'Remove header', 'uncanny-automator-pro' ),
			);
		}
		if ( $options['fields'] ) {
			$webhook_fields[] = array(
				'option_code'       => 'WEBHOOK_FIELDS',
				'input_type'        => 'repeater',
				'label'             => $options['fields_label'],
				'relevant_tokens'   => array(),
				/* translators: 1. Button */
				'description'       => $options['fields_description'],
				'required'          => true,
				'fields'            => array(
					array(
						'option_code'     => 'KEY',
						'label'           => __( 'Key', 'uncanny-automator-pro' ),
						'input_type'      => 'text',
						'required'        => true,
						'supports_tokens' => false,
					),
					array(
						'option_code' => 'VALUE_TYPE',
						'label'       => __( 'Type', 'uncanny-automator-pro' ),
						'input_type'  => 'select',
						'required'    => true,
						'options'     => self::get_webhook_data_types(),
					),
					array(
						'option_code'     => 'SAMPLE_VALUE',
						'label'           => __( 'Sample value', 'uncanny-automator-pro' ),
						'input_type'      => 'text',
						'required'        => false,
						'supports_tokens' => false,
					),
				),
				'add_row_button'    => __( 'Add pair', 'uncanny-automator-pro' ),
				'remove_row_button' => __( 'Remove pair', 'uncanny-automator-pro' ),
			);
		}

		return array(
			'WEBHOOK_DATA' => $webhook_fields,
		);
	}

	/**
	 * "Get samples" button Webhook
	 *
	 * @return array[]
	 */
	public static function get_webhook_get_sample_button( $text = null ) {
		if ( empty( $text ) ) {
			$text = __( 'Get samples', 'uncanny-automator-pro' );
		}

		return array(
			array(
				'show_in'     => 'WEBHOOK_DATA',
				/* translators: Button. Non-personal infinitive verb */
				'text'        => $text,
				'css_classes' => 'uap-btn uap-btn--red',
				'on_click'    => Webhook_Static_Content::get_samples_js(),
				'modules'     => array( 'modal', 'markdown' ),
			),
		);
	}

	/**
	 * Common `run_webhook` action handler
	 *
	 * @param $trigger_code
	 * @param $trigger_meta
	 * @param $param
	 * @param $recipe
	 *
	 * @return void
	 */
	public static function run_webhook( $trigger_code = null, $trigger_meta = null, $param = array(), $recipe = array(), $request = null ) {
		$user_id = get_current_user_id();

		$args = array(
			'code'           => $trigger_code,
			'meta'           => $trigger_meta,
			'ignore_post_id' => true,
			'webhook_recipe' => $recipe['ID'],
			'is_webhook'     => true,
			'user_id'        => $user_id,
		);

		$args = Automator()->process->user->maybe_add_trigger_entry( $args, false );
		if ( empty( $args ) ) {
			return;
		}

		//Adding an action for other usage of API Data.
		do_action( 'automator_api_received', $param, $recipe );

		// Save trigger meta
		foreach ( $args as $result ) {
			if ( true !== $result['result'] ) {
				continue;
			}
			if ( empty( $param ) ) {
				continue;
			}

			$trigger_id     = absint( $result['args']['trigger_id'] );
			$trigger_log_id = absint( $result['args']['trigger_log_id'] );
			$run_number     = absint( $result['args']['run_number'] );
			$trigger_meta   = array(
				'user_id'        => $user_id,
				'trigger_id'     => $trigger_id,
				'trigger_log_id' => $trigger_log_id,
				'run_number'     => $run_number,
			);
			Automator()->db->token->save( 'WEBHOOK_BODY', maybe_serialize( $param['WEBHOOK_BODY'] ), $trigger_meta );
			$tokens = array();
			foreach ( $param['params'] as $data ) {
				$tokens[ $data['meta_key'] ] = maybe_serialize( $data['meta_value'] );
			}

			// Directly use the function since it will Fatal with Traits. We can use Traits after sometime or after we built an "Adapter" for it like we did with Action tokens.
			if ( method_exists( Automator()->helpers->recipe, 'set_trigger_log_properties' ) ) {
				self::insert_properties( $request, $tokens, $param );
			}

			Automator()->db->token->save( "{$trigger_code}_parsed", maybe_serialize( $tokens ), $trigger_meta );

			Automator()->process->user->maybe_trigger_complete( $result['args'] );

		}
	}

	/**
	 * Inserts webhook properties.
	 *
	 * @param WP_REST_Request $request
	 * @param mixed[] $tokens
	 * @param mixed[] $param
	 *
	 * @return bool Returns true if properties was successfully inserted. Otherwise, false.
	 */
	public static function insert_properties( $request, $tokens, $param ) {

		if ( ! $request instanceof WP_REST_Request ) {
			return false;
		}

		$body = htmlentities( $request->get_body(), ENT_QUOTES );

		$properties = array(
			// The actual request headers processed by WP.
			array(
				'type'       => 'code',
				'label'      => _x( 'Request headers', 'Webhook', 'uncanny-automator' ),
				'value'      => (array) wp_json_encode( (array) $request->get_headers() ),
				'attributes' => array(
					'code_language' => 'json',
				),
			),
			// The actual body payload processed by WP.
			array(
				'type'  => 'text',
				'label' => _x( 'Request body (raw)', 'Webhook', 'uncanny-automator' ),
				'value' => ! empty( $body ) ? $body : '<empty>',
			),
			// The actual parameters processed by WP.
			array(
				'type'       => 'code',
				'label'      => _x( 'Request parameters (raw)', 'Webhook', 'uncanny-automator' ),
				'value'      => (array) wp_json_encode( (array) $request->get_params() ),
				'attributes' => array(
					'code_language' => 'json',
				),
			),
			// The actual request parameters processed by Uncanny Automator.
			array(
				'type'       => 'code',
				'label'      => _x( 'Request parameters (parsed)', 'Webhook', 'uncanny-automator' ),
				'value'      => (array) wp_json_encode( (array) $tokens ),
				'attributes' => array(
					'code_language' => 'json',
				),
			),
			// The actual parameters processed by Uncanny Automator.
			array(
				'type'       => 'code',
				_x( 'Processed parameters', 'Webhook', 'uncanny-automator' ),
				'value'      => (array) wp_json_encode( (array) $param ),
				'attributes' => array(
					'code_language' => 'json',
				),
			),
		);

		Automator()->helpers->recipe->set_trigger_log_properties( $properties );

		return true;

	}

	/**
	 * Attempt to auto-detect the given value
	 *
	 * @param $key
	 * @param $value
	 *
	 * @return mixed|void
	 */
	public static function value_maybe_of_type( $key, $value ) {
		$type = 'text';

		if ( is_array( $value ) || is_object( $value ) ) {
			return apply_filters( 'automator_pro_webhook_value_of_type_array', $type, $key, $value );
		}

		if ( is_email( $value ) ) {
			$type = 'email';

			return apply_filters( 'automator_pro_webhook_value_of_type_email', $type, $key, $value );
		}

		if ( is_float( $value ) ) {
			$type = 'float';

			return apply_filters( 'automator_pro_webhook_value_of_type_float', $type, $key, $value );
		}

		if ( is_numeric( $value ) ) {
			$type = 'int';

			return apply_filters( 'automator_pro_webhook_value_of_type_int', $type, $key, $value );
		}

		if ( wp_http_validate_url( $value ) ) {
			$type = 'url';

			return apply_filters( 'automator_pro_webhook_value_of_type_url', $type, $key, $value );
		}

		return apply_filters( 'automator_pro_webhook_value_of_type_text', $type, $key, $value );
	}

	/**
	 * Common token parsing for webhook tokens
	 *
	 * @param $value
	 * @param $pieces
	 * @param $recipe_id
	 * @param $trigger_data
	 * @param $user_id
	 * @param $replace_args
	 *
	 * @return mixed|string
	 */
	public static function parse_webhook_tokens( $value, $pieces, $recipe_id, $trigger_data, $user_id, $replace_args ) {
		$matched = array_intersect( Webhook_Rest_Handler::get_trigger_codes(), $pieces );
		if ( empty( $matched ) ) {
			return $value;
		}

		if ( empty( $trigger_data ) ) {
			return $value;
		}

		foreach ( $trigger_data as $trigger ) {
			$trigger_id     = absint( $trigger['ID'] ?? null );
			$trigger_log_id = absint( $replace_args['trigger_log_id'] );
			$parse_tokens   = array(
				'trigger_id'     => $trigger_id,
				'trigger_log_id' => $trigger_log_id,
				'user_id'        => $user_id,
			);

			if ( in_array( 'WEBHOOK_URL', $pieces, true ) ) {
				$meta_value = get_post_meta( $trigger_id, 'WEBHOOK_URL', true );
				if ( ! empty( $meta_value ) ) {
					return maybe_unserialize( $meta_value );
				}
			}

			if ( in_array( 'DATA_FORMAT', $pieces, true ) ) {
				$meta_value = get_post_meta( $trigger_id, 'DATA_FORMAT', true );
				if ( ! empty( $meta_value ) ) {
					return maybe_unserialize( $meta_value );
				}
			}

			if ( in_array( 'WEBHOOK_HEADERS', $pieces, true ) ) {
				$meta_value = get_post_meta( $trigger_id, 'WEBHOOK_HEADERS', true );
				if ( ! empty( $meta_value ) ) {
					return maybe_unserialize( $meta_value );
				}

				return '';
			}

			if ( in_array( 'WEBHOOK_BODY', $pieces, true ) ) {
				$meta_value = Automator()->db->trigger->get_token_meta( 'WEBHOOK_BODY', $parse_tokens );
				if ( ! empty( $meta_value ) ) {
					if ( true === apply_filters( 'automator_pre_wrap_webhook_body', true, $pieces, $parse_tokens ) ) {
						return sprintf( '<pre>%s</pre>', maybe_unserialize( $meta_value ) );
					}

					return maybe_unserialize( $meta_value );
				}
			}

			if ( in_array( 'WEBHOOK_SAMPLE', $pieces, true ) ) {
				$meta_value = get_post_meta( $trigger_id, 'WEBHOOK_SAMPLE', true );
				if ( ! empty( $meta_value ) ) {
					if ( true === apply_filters( 'automator_pre_wrap_webhook_sample', true, $pieces, $parse_tokens ) ) {
						return sprintf( '<pre>%s</pre>', maybe_unserialize( $meta_value ) );
					}

					return maybe_unserialize( $meta_value );
				}
			}

			// Parse the sample value.
			if ( isset( $pieces[2] ) && strpos( $pieces[2], 'SAMPLE_VALUE' ) !== false ) {
				$meta_value = json_decode( get_post_meta( $trigger_id, 'WEBHOOK_FIELDS', true ) );
				if ( ! empty( $meta_value ) ) {
					$subpieces = explode( '|', $pieces[2] );
					if ( is_array( $subpieces ) ) {
						$value = isset( $meta_value[ $subpieces[1] ]->SAMPLE_VALUE ) ? $meta_value[ $subpieces[1] ]->SAMPLE_VALUE : '';
						if ( is_array( $value ) ) {
							$value = implode( ', ', $value );
						}
					}
				}

				return $value;
			}

			// Parse the actual token value.
			$meta_key = sprintf( '%s_parsed', array_shift( $matched ) );
			$entry    = Automator()->db->trigger->get_token_meta( $meta_key, $parse_tokens );

			if ( empty( $entry ) ) {
				continue;
			}

			$value = maybe_unserialize( $entry );
			if ( is_array( $value ) && isset( $pieces[3] ) ) {
				$value = isset( $entry[ $pieces[3] ] ) ? $entry[ $pieces[3] ] : '';
				if ( is_array( $value ) ) {
					$value = implode( ', ', $value );
				}
			}
		}

		return $value;
	}

	/**
	 * @param $tokens
	 * @param $args
	 *
	 * @return array
	 */
	public function webhooks_possible_tokens( $tokens = array(), $args = array() ) {
		$trigger_meta = isset( $args['triggers_meta'] ) && isset( $args['triggers_meta']['code'] ) ? $args['triggers_meta']['code'] : null;
		if ( null === $trigger_meta ) {
			return $tokens;
		}

		// Add tokens for webhook fields.
		if ( ! empty( $args['triggers_meta']['WEBHOOK_FIELDS'] ) ) {
			$webhook_fields = json_decode( $args['triggers_meta']['WEBHOOK_FIELDS'] );
			$fields         = 1;
			if ( ! empty( $webhook_fields ) ) {
				foreach ( $webhook_fields as $k => $field ) {
					$tokens[] = array(
						'tokenId'         => sprintf( 'WEBHOOK_FIELDS|%d|KEY:%s', $k, $field->KEY ),
						'tokenName'       => sprintf( '%s#%d %s', __( 'Field', 'uncanny-automator-pro' ), $fields, $field->KEY ),
						'tokenType'       => $field->VALUE_TYPE,
						'tokenIdentifier' => $trigger_meta,
					);
					$tokens[] = array(
						'tokenId'         => sprintf( 'WEBHOOK_FIELDS|%d|%s', $k, 'SAMPLE_VALUE' ),
						'tokenName'       => sprintf( '%s#%d %s %s', __( 'Field', 'uncanny-automator-pro' ), $fields, $field->KEY, __( '(original sample)', 'uncanny-automator-pro' ) ),
						'tokenType'       => $field->VALUE_TYPE,
						'tokenIdentifier' => $trigger_meta,
					);
					$fields ++;
				}
			}
		}

		$tokens[] = array(
			'tokenId'         => 'WEBHOOK_BODY',
			'tokenName'       => __( 'Webhook body', 'uncanny-automator-pro' ),
			'tokenType'       => 'text',
			'tokenIdentifier' => $trigger_meta,
		);
		$tokens[] = array(
			'tokenId'         => 'WEBHOOK_SAMPLE',
			'tokenName'       => __( 'Webhook sample data', 'uncanny-automator-pro' ),
			'tokenType'       => 'text',
			'tokenIdentifier' => $trigger_meta,
		);

		return $tokens;
	}
}
