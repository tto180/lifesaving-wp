<?php
namespace Uncanny_Automator_Pro\Loops\Token;

use WP_Error;

/**
 * Text_Parseable.
 *
 * Text processor class. Use this class as a reference to build Array_Parseable (Loopable tokens).
 *
 * @since 5.3
 *
 * @package Uncanny_Automator_Pro\Loops\Token
 */
abstract class Text_Parseable {

	/**
	 * The regexp pattern. Extend this with your own regular expression pattern.
	 *
	 * @var string $pattern
	 */
	protected $pattern = '';

	/**
	 * The filter tag string.
	 *
	 * @see \Uncanny_Automator\Automator_Input_Parser
	 *
	 * @var string
	 */
	protected static $parser_filter_tag = 'automator_token_parser_extended_loop_token';

	/**
	 * The text parser arguments.
	 *
	 * @var mixed[]
	 */
	protected $text_parser_args = array();

	/**
	 * Avoid class __constructore overwrite since filter registration done through static().
	 *
	 * @return void
	 */
	final public function __construct() {
		$this->init();
	}

	/**
	 * Concrete class can ovewrite this method to.
	 *
	 * @return false
	 */
	protected function init() {
		return false;
	}

	/**
	 * Registers the parser.
	 *
	 * @see \Uncanny_Automator\Automator_Input_Parser
	 *
	 * @return void
	 */
	public static function register_parser() {
		add_filter( self::$parser_filter_tag, array( new static(), 'parse_tokens' ), 10, 4 );
	}

	/**
	 * Allow concrete class to parse their own value.
	 *
	 * @param int $entity_id
	 * @param string $extracted_token
	 *
	 * @return mixed
	 */
	abstract public function parse( $entity_id, $extracted_token );

	/**
	 * Generates a key value pairs. Its up to the concrete class on how they would create key value pairs.
	 *
	 * @param int $entity_id
	 * @param string[]|WP_Error $extracted_tokens
	 *
	 * @return mixed[]
	 */
	protected function make_key_value_pairs( $entity_id, $extracted_tokens ) {

		$key_value_pairs = array();

		if ( is_wp_error( $extracted_tokens ) ) {
			return array();
		}

		foreach ( (array) $extracted_tokens as $extracted_token ) {

			$extracted_token_parts = (array) explode( ':', $extracted_token );

			// Remove the trailing '}}'.
			$token = str_replace( '}}', '', $extracted_token_parts[4] );

			/**
			 *! Concrete class completes the parsing.
			 */
			$key_value_pair = $this->parse( $entity_id, $token );

			// Store.
			$key_value_pairs[ $extracted_token ] = $key_value_pair;

		}

		return $key_value_pairs;

	}

	/**
	 * Sets the text parser arguments.
	 *
	 * @param mixed[] $parser_args
	 *
	 * @return void
	 */
	public function set_text_parser_args( $parser_args ) {
		$this->text_parser_args = $parser_args;
	}

	/**
	 * Returns the text parser arguments.
	 *
	 * @return mixed[]
	 */
	public function get_text_parser_args() {
		return $this->text_parser_args;
	}

	/**
	 * Extracts the tokens from the given text.
	 *
	 * @param string $string Usually the field text value.
	 *
	 * @return WP_Error|string[]
	 */
	private function extract_tokens( $string ) {

		preg_match_all( $this->pattern, $string, $regxp_user_tokens );

		if ( ! isset( $regxp_user_tokens[0] ) || empty( $regxp_user_tokens[0] ) ) {
			return new WP_Error( 400, 'Cannot extract regxp user tokens from the field text.' );
		}

		return $regxp_user_tokens[0];

	}

	/**
	 * Parses the tokens.
	 *
	 * @param string $field_text
	 * @param string $match
	 * @param mixed[] $args The parameters inside the token parser.
	 * @param mixed[] $text_parser_args The text parser args. The variable is named $trigger_args in parser.
	 *
	 * @return string
	 */
	public function parse_tokens( $field_text, $match, $args, $text_parser_args ) {

		// Early bail if required argument is not set.
		if ( ! isset( $text_parser_args['loop'] )
			|| ! is_array( $text_parser_args['loop'] )
			|| ! isset( $text_parser_args['loop']['entity_id'] ) ) {
			return $field_text;
		}

		$this->set_text_parser_args( $text_parser_args );

		// Early bail if pattern is not set by the concrete implementation.
		if ( empty( $this->pattern ) ) {
			return $field_text;
		}

		$extracted_tokens = $this->extract_tokens( $field_text );

		if ( is_wp_error( $extracted_tokens ) ) {
			return $field_text;
		}

		$token_value_pairs = $this->make_key_value_pairs(
			$text_parser_args['loop']['entity_id'], // Refers to the user id.
			$extracted_tokens
		);

		return strtr( $field_text, $token_value_pairs );

	}

}

