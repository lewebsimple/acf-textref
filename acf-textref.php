<?php
/**
 * Plugin Name:     ACF Text Reference
 * Plugin URI:      https://github.com/lewebsimple/acf-textref
 * Description:     Text reference field for Advanced Custom Fields v5.
 * Author:          Pascal Martineau <pascal@lewebsimple.ca>
 * Author URI:      https://lewebsimple.ca
 * License:         GPLv2 or later
 * License URI:     http://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:     acf-textref
 * Domain Path:     /languages
 * Version:         1.2.6
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'acf_textref_plugin' ) ) {

	class acf_textref_plugin {

		public $settings;

		function __construct() {
			$this->settings = array(
				'version' => '1.2.6',
				'url'     => plugin_dir_url( __FILE__ ),
				'path'    => plugin_dir_path( __FILE__ )
			);
			add_action( 'acf/include_field_types', array( $this, 'include_field_types' ) );
		}

		function include_field_types( $version ) {
			load_plugin_textdomain( 'acf-textref', false, plugin_basename( dirname( __FILE__ ) ) . '/languages' );
			include_once( 'fields/class-acf-textref-v5.php' );
		}

		/**
		 * Convert array value to different formats
		 *
		 * @param $value
		 * @param array $field
		 *
		 * @return string|array
		 */
		static function format_value( $value, $field = array() ) {
			$field = wp_parse_args( $field, array(
				'return_format' => 'string',
				'multiple'      => true,
				'separator'     => ';',
				'link_class'    => '',
			) );

			// Force array value
			if ( ! is_array( $value ) ) {
				$value = array();
			}

			switch ( $field['return_format'] ) {
				case 'string':
					return implode( $field['separator'], array_map( function ( $textref ) {
						if ( empty( $textref['post_id'] ) ) {
							return isset( $textref['text'] ) ? $textref['text'] : '';
						} else {
							return sprintf( '%s [%d]', $textref['text'], $textref['post_id'] );
						}
					}, $value ) );

				case 'list':
					return empty( $value ) ? '' : '<ul><li>' . implode( '</li><li>', array_map( function ( $textref ) use ( $field ) {
							if ( empty( $textref['post_id'] ) ) {
								return isset( $textref['text'] ) ? $textref['text'] : '';
							} else {
								return sprintf( '<a href="%s" class="%s">%s</a>', get_the_permalink( $textref['post_id'] ), $field['link_class'], $textref['text'] );
							}
						}, $value ) ) . '</li></ul>';

				case 'inline':
					return implode( $field['separator'] . ' ', array_map( function ( $textref ) use ( $field ) {
						if ( empty( $textref['post_id'] ) ) {
							return $textref['text'];
						} else {
							return sprintf( '<a href="%s" class="%s">%s</a>', get_the_permalink( $textref['post_id'] ), $field['link_class'], $textref['text'] );
						}
					}, $value ) );

				case 'array':
				default:
					return $value;

			}
		}

		/**
		 * Convert string value to array format ['text' => '...', 'post_id' => '...']
		 *
		 * @param $value
		 * @param $field
		 *
		 * @return array
		 */
		static function parse_value( $value, $field ) {
			if ( is_array( $value ) ) {
				return $value;
			}
			if ( empty( $value ) || ! is_string( $value ) ) {
				return array();
			}

			$field = wp_parse_args( $field, array(
				'post_type' => 'post',
				'separator' => ';',
			) );

			// Separate the values into an array, eliminate empty ones
			$value = array_filter( explode( $field['separator'], $value ) );

			// Parse the values and convert each one to array('text' => '...', 'post_id' => '...')
			$value = array_map( function ( $single_value ) use ( $field ) {
				$single_value = trim( $single_value );
				$text         = $single_value;
				$post_id      = null;

				if ( preg_match( '/(.*) \[(\d+)\]$/', $single_value, $matches ) ) {
					if ( get_post_type( $matches[2] ) === $field['post_type'] ) {
						$post_id = end( $matches );
						$text    = get_the_title( $post_id );
					} else {
						$text = $matches[1];
					}
				} elseif ( ! empty( $post = get_page_by_title( $single_value, OBJECT, $field['post_type'] ) ) && $post->post_title === $single_value ) {
					$post_id = $post->ID;
				}

				return array(
					'text'    => $text,
					'post_id' => (int) $post_id,
				);
			}, $value );

			return $value;
		}

	}

	new acf_textref_plugin();

}
