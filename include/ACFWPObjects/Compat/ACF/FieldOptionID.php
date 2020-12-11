<?php

namespace ACFWPObjects\Compat\ACF;

use ACFWPObjects\Core;

class FieldOptionID extends Core\Singleton {

	/**
	 *	@inheritdoc
	 */
	protected function __construct() {

		add_action( 'acf/render_field_settings/type=text', [ $this, 'field_settings' ] );
		add_filter( 'acf/prepare_field/type=text', [ $this, 'prepare_field' ] );
		add_filter( 'acf/update_value/type=text', [ $this, 'update_value' ], 10, 3 );
	}


	public function update_value( $value, $post_id, $field ) {
		$field = wp_parse_args( $field, [ 'is_id' => false ] );
		if ( $field['is_id'] ) {
			// $value = sanitize_title($value);
			// $value = sanitize_key($value);
		}
		return $value;
	}

	public function prepare_field( $field ) {

		$field = wp_parse_args( $field, [ 'is_id' => false, 'is_id_once' => false ] );

		if ( $field['is_id'] ) {
			// $field['pattern'] = '[0-9a-z_-]+';
			$field['required'] = 1;
			$field['wrapper'] = wp_parse_args( $field['wrapper'], [
				'class' => '',
			] );

			$field['wrapper']['class'] .= ' acf-id-field';

			if ( ! empty( $field[ 'value' ] ) && $field['is_id_once'] ) {
				$field['readonly'] = 1;
			}

		}
		return $field;
	}

	public function field_settings( $field ) {

		// allow_null
		acf_render_field_setting( $field, array(
			'label'			=> __('ID Field','acf-wp-objects'),
			'instructions'	=> __('ID Fields are always unique and required.', 'acf-wp-objects' ),
			'name'			=> 'is_id',
			'type'			=> 'true_false',
			'ui'			=> 1,
		));

		acf_render_field_setting( $field, array(
			'label'			=> __('Edit Once','acf-wp-objects'),
			'instructions'	=> __('Disable input, if field has a value.', 'acf-wp-objects' ),
			'name'			=> 'is_id_once',
			'type'			=> 'true_false',
			'ui'			=> 1,
			'conditions'	=> [
				'field'		=> 'is_id',
				'operator'	=> '==',
				'value'		=> 1
			],
		));

	}

}
