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
 * Version:         1.1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'acf_textref_plugin' ) ) {

	class acf_textref_plugin {

		public $settings;

		function __construct() {
			$this->settings = array(
				'version' => '1.1.0',
				'url'     => plugin_dir_url( __FILE__ ),
				'path'    => plugin_dir_path( __FILE__ )
			);
			add_action( 'acf/include_field_types', array( $this, 'include_field_types' ) );
		}

		function include_field_types( $version ) {
			load_plugin_textdomain( 'acf-textref', false, plugin_basename( dirname( __FILE__ ) ) . '/languages' );
			include_once( 'fields/class-acf-textref-v5.php' );
		}

	}

	new acf_textref_plugin();

}
