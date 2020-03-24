<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'acf_textref_field' ) ) {

	class acf_textref_field extends acf_field {

		public $settings;

		function __construct( $settings ) {
			$this->name     = 'textref';
			$this->label    = __( "Text Reference", 'acf-textref' );
			$this->category = 'relational';
			$this->defaults = array(
				'post_type' => 'post',
			);
			$this->settings = $settings;
			parent::__construct();
		}

		/**
		 * Render textref field settings
		 *
		 * @param $field (array) the $field being edited
		 */
		function render_field_settings( $field ) {
			// Post Type
			acf_render_field_setting( $field, array(
				'label'   => __( "Referenced post type", 'acf-textref' ),
				'type'    => 'select',
				'name'    => 'post_type',
				'choices' => acf_get_pretty_post_types(),
				'ui'      => 1,
			) );
		}

		/**
		 * Render textref field input
		 *
		 * @param $field (array) the $field being rendered
		 */
		function render_field( $field ) {
			$name  = $field['name'];
			$value = $field['value'];
			?>
            <div class="acf-input-wrap acf-textref">
                <input type="text" name="<?= $name ?>" value="<?= $value ?>"/>
            </div>
			<?php
		}

		/**
		 * Format textref value
		 *
		 * @param $value (mixed) the value which was loaded from the database
		 * @param $post_id (mixed) the $post_id from which the value was loaded
		 * @param $field (array) the $field array holding the options
		 *
		 * @return $value (mixed) the formatted value
		 */
		function format_value( $value, $post_id, $field ) {
			if ( ! empty( $post = get_page_by_title( $value, OBJECT, $field['post_type'] ) ) && $post->post_title === $value ) {
				return sprintf( '<a href="%1$s">%2$s</a>', get_the_permalink( $post->ID ), $value );
			} else {
				return $value;
			}
		}

	}

	new acf_textref_field( $this->settings );

}
