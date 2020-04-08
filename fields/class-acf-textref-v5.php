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
				'link_class'    => '',
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
					'array'  => __( "Values (array)", 'acf-textref' ),
					'string' => __( "String", 'acf-textref' ),
					'list'   => __( "List", 'acf-textref' ),
					'inline' => __( "Inline", 'acf-textref' ),
				),
			) );
			// Link Class
			acf_render_field_setting( $field, array(
				'label'             => __( "Link class", 'acf-textref' ),
				'instructions'      => __( "Specify the class used for the link.", 'acf-textref' ),
				'type'              => 'text',
				'name'              => 'link_class',
				'conditional_logic' => array(
					array(
						array(
							'field'    => 'return_format',
							'operator' => '==',
							'value'    => 'list',
						),
					),
					array(
						array(
							'field'    => 'return_format',
							'operator' => '==',
							'value'    => 'inline',
						),
					),
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
			$value = acf_textref_plugin::format_value( $field['value'], wp_parse_args( array( 'return_format' => 'string' ), $field ) );
			?>
            <div class="acf-input-wrap acf-textref">
                <input type="text" name="<?= $name ?>" value="<?= $value ?>"/>
            </div>
			<?php
		}

		/**
		 * Update value in array format
		 *
		 * @param    $value (mixed) the value found in the database
		 * @param    $post_id (mixed) the $post_id from which the value was loaded
		 * @param    $field (array) the field array holding all the field options
		 *
		 * @return   $value
		 */
		function update_value( $value, $post_id, $field ) {
			return acf_textref_plugin::parse_value( $value, wp_parse_args( array( 'return_format' => 'array' ), $field ) );
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
			return acf_textref_plugin::format_value( $value, $field );
		}

	}

	new acf_textref_field( $this->settings );

}
