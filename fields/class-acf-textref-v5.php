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
				'post_type'     => 'post',
				'multiple'      => 0,
				'separator'     => ';',
				'return_format' => 'array',
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
			// Multiple Values
			acf_render_field_setting( $field, array(
				'label' => __( "Allow multiple values", 'acf-textref' ),
				'type'  => 'true_false',
				'name'  => 'multiple',
				'ui'    => 1,
			) );
			// Separator
			acf_render_field_setting( $field, array(
				'label'             => __( "Separator", 'acf-textref' ),
				'type'              => 'text',
				'name'              => 'separator',
				'conditional_logic' => array(
					array(
						array(
							'field'    => 'multiple',
							'operator' => '==',
							'value'    => '1',
						),
					),
				),
			) );
			// Return_format
			acf_render_field_setting( $field, array(
				'label'        => __( "Return format", 'acf-textref' ),
				'instructions' => __( "Specify the return format used in the templates.", 'acf-textref' ),
				'type'         => 'select',
				'name'         => 'return_format',
				'choices'      => array(
					'array' => __( "Values (array)", 'acf-textref' ),
				),
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
		 *  Load textref value
		 *
		 * @param    $value (mixed) the value found in the database
		 * @param    $post_id (mixed) the $post_id from which the value was loaded
		 * @param    $field (array) the field array holding all the field options
		 *
		 * @return    $value
		 */
		function load_value( $value, $post_id, $field ) {
			if ( ! is_array( $value ) ) {
				return $value;
			}
			if ( ! empty( $field['multiple'] ) ) {
				return implode( $field['separator'], array_map( function ( $single_value ) {
					return $single_value['text'];
				}, $value ) );
			} else {
				return $value['text'];
			}
		}

		static function update_single_value( $value, $field ) {
			$post_id = null;
			if ( ! empty( $post = get_page_by_title( $value, OBJECT, $field['post_type'] ) ) && $post->post_title === $value ) {
				$post_id = $post->ID;

			}

			return array(
				'text'    => $value,
				'post_id' => $post_id,
			);
		}

		/**
		 *  update_value()
		 *
		 * @param    $value (mixed) the value found in the database
		 * @param    $post_id (mixed) the $post_id from which the value was loaded
		 * @param    $field (array) the field array holding all the field options
		 *
		 * @return   $value
		 */
		function update_value( $value, $post_id, $field ) {
			if ( ! empty( $field['multiple'] ) ) {
				$value = array_map( function ( $single_value ) use ( $field ) {
					return acf_textref_field::update_single_value( trim( $single_value ), $field );
				}, explode( $field['separator'], $value ) );
			} else {
				$value = acf_textref_field::update_single_value( $value, $field );
			}

			return $value;
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
			switch ( $field['return_format'] ) {
				case 'array':
				default:
					return $value;
			}
		}

	}

	new acf_textref_field( $this->settings );

}
