<?php


namespace ACFWPObjects\Compat\ACF;

use ACFWPObjects\Core;

class RepeaterChoices extends Core\Singleton {

	/**
	 *	@var array field types that can serve as value or label fields mapped to their rendering function
	 */
	private $allow_fields = [
		'text'				=> 'text',
		'number'			=> 'text',
		'range'				=> 'text',
		'url'				=> 'text',
		'email'				=> 'text',
		'date_picker'		=> 'text',
		'time_picker'		=> 'text',
		'date_time_picker'	=> 'text',
		'image'				=> 'image',
		'color_picker'		=> 'color',
		'select'			=> 'text',
		'radio'				=> 'text'
	];
	private $repeater_fields = null;

	private $value_cache = [];

	/**
	 *	@inheritdoc
	 */
	protected function __construct( ) {

		add_filter( 'acf/prepare_field/type=select', [ $this, 'prepare' ] );
		add_filter( 'acf/prepare_field/type=radio', [ $this, 'prepare' ] );
		add_filter( 'acf/prepare_field/type=button_group', [ $this, 'prepare' ] );
		add_filter( 'acf/prepare_field/type=checkbox', [ $this, 'prepare' ] );

		add_filter( 'acf/load_field/type=select', [ $this, 'migrate_field' ], 5 );
		add_filter( 'acf/load_field/type=radio', [ $this, 'migrate_field' ], 5 );
		add_filter( 'acf/load_field/type=button_group', [ $this, 'migrate_field' ], 5 );
		add_filter( 'acf/load_field/type=checkbox', [ $this, 'migrate_field' ], 5 );

		add_action( 'acf/render_field_settings/type=select', [ $this, 'add_choice_filter' ], 9 );
		add_action( 'acf/render_field_settings/type=radio', [ $this, 'add_choice_filter' ], 9 );
		add_action( 'acf/render_field_settings/type=button_group', [ $this, 'add_choice_filter' ], 9 );
		add_action( 'acf/render_field_settings/type=checkbox', [ $this, 'add_choice_filter' ], 9 );

		add_action( 'acf/render_field_settings/type=select', [ $this, 'render_settings' ] );
		add_action( 'acf/render_field_settings/type=radio', [ $this, 'render_settings' ] );
		add_action( 'acf/render_field_settings/type=button_group', [ $this, 'render_settings' ] );
		add_action( 'acf/render_field_settings/type=checkbox', [ $this, 'render_settings' ] );

		add_action( 'acf/render_field_settings/type=select', [ $this, 'remove_choice_filter' ], 11 );
		add_action( 'acf/render_field_settings/type=radio', [ $this, 'remove_choice_filter' ], 11 );
		add_action( 'acf/render_field_settings/type=button_group', [ $this, 'remove_choice_filter' ], 11 );
		add_action( 'acf/render_field_settings/type=checkbox', [ $this, 'remove_choice_filter' ], 11 );

		add_filter( 'acf/format_value/type=select', [ $this, 'format_value' ], 15, 3 );
		add_filter( 'acf/format_value/type=radio', [ $this, 'format_value' ], 15, 3 );
		add_filter( 'acf/format_value/type=button_group', [ $this, 'format_value' ], 15, 3 );
		add_filter( 'acf/format_value/type=checkbox', [ $this, 'format_value' ], 15, 3 );

	}

	/**
	 *	@action
	 */
	public function add_choice_filter( $field ) {

		add_filter( 'acf/validate_field', [ $this, 'validate_return_format_field' ] );

	}

	/**
	 *	@action
	 */
	public function remove_choice_filter( $field ) {

		remove_filter( 'acf/validate_field', [ $this, 'validate_return_format_field' ] );

	}

	/**
	 *	@filter acf/format_value/?type=*
	 */
	public function format_value( $value, $post_id, $field ) {

		if ( ! isset( $field['repeater_choices'] ) || ! $field['repeater_choices'] || $field['return_format'] !== 'row' ) {
			return $value;
		}

		$cache_key = sprintf( '%s-%s-%s', $field['repeater_field'], $field['repeater_post_id'], $value );

		if ( isset( $this->value_cache[ $cache_key ] ) ) {
			return $this->value_cache[ $cache_key ];
		}

		// generate value cache
		while ( have_rows( $field['repeater_field'], $field['repeater_post_id'] ) ) {
			the_row();
			$raw_row = get_row( false );

			$key = sprintf( '%s-%s-%s', $field['repeater_field'], $field['repeater_post_id'], sanitize_key( $raw_row[ $field['repeater_value_field'] ] ) );
			$this->value_cache[ $key ] = get_row( true );

			if ( $cache_key === $key ) {
				$value = $this->value_cache[ $key ];
			}
		}
		return $value;
	}

	/**
	 *	@filter acf/render_field_settings/type=*
	 */
	public function validate_return_format_field( $field ) {

		if ( $field['name'] === 'return_format' && isset( $field['choices'] ) ) {
			$field['choices']['row'] = __('Repeater Row', 'acf-wp-objects' );
		}
		return $field;
	}

	/**
	 *	@filter acf/render_field_settings/type=*
	 */
	public function render_settings( $field ) {

		$repeater_field_choices = $this->get_first_level_repeater_fields();
		$value_field_choices = $label_field_choices = $this->get_repeated_fields( $repeater_field_choices );

		// enable
		acf_render_field_setting( $field, [
			'label'			=> __('Get choices from repeater','acf-wp-objects'),
			'instructions'	=> '',
			'name'			=> 'repeater_choices',
			'type'			=> 'true_false',
			'ui'			=> 1,
		]);

		// repeater field
		acf_render_field_setting( $field, [
			'label'			=> __('Repeater Field','acf-wp-objects'),
			'instructions'	=> '',
			'name'			=> 'repeater_field',
			'type'			=> 'select',
			'ui'			=> 0,
			'allow_null'	=> 1,
			'choices'		=> $repeater_field_choices,
			'conditions'	=> [
				'field'		=> 'repeater_choices',
				'operator'	=> '==',
				'value'		=> 1
			]
		]);


		// value field
		acf_render_field_setting( $field, [
			'label'			=> __('Value Field','acf-wp-objects'),
			'instructions'	=> '',
			'name'			=> 'repeater_value_field',
			'type'			=> 'select',
			'ui'			=> 0,
			'allow_null'	=> 0,
			'choices'		=> $value_field_choices,
			'conditions'	=> [
				[
					'field'		=> 'repeater_choices',
					'operator'	=> '==',
					'value'		=> 1
				],
				[
					'field'		=> 'repeater_field',
					'operator'	=> '!=empty',
				],
			]
		]);
		// label field
		acf_render_field_setting( $field, [
			'label'			=> __('Label Field','acf-wp-objects'),
			'instructions'	=> '',
			'name'			=> 'repeater_label_field',
			'type'			=> 'select',
			'ui'			=> 0,
			'allow_null'	=> 1,
			'choices'		=> $label_field_choices,
			'conditions'	=> [
				[
					'field'		=> 'repeater_choices',
					'operator'	=> '==',
					'value'		=> 1
				],
				[
					'field'		=> 'repeater_field',
					'operator'	=> '!=empty',
				],
			]
		]);
		if ( $field['type'] !== 'select' ) {
			// enable
			acf_render_field_setting( $field, [
				'label'			=> __('Visualize field','acf-wp-objects'),
				'instructions'	=> '',
				'name'			=> 'repeater_display_field',
				'type'			=> 'select',
				'ui'			=> 0,
				'allow_null'	=> 1,
				'choices'		=> $label_field_choices,
				'conditions'	=> [
					[
						'field'		=> 'repeater_choices',
						'operator'	=> '==',
						'value'		=> 1
					],
					[
						'field'		=> 'repeater_field',
						'operator'	=> '!=empty',
					],
				]
			]);
		}

		// post id
		acf_render_field_setting( $field, [
			'label'			=> __('Post ID','acf-wp-objects'),
			'instructions'	=> __('Leave empty for current Post ID.','acf-wp-objects'),
			'name'			=> 'repeater_post_id',
			'type'			=> 'text',
			'placeholder'	=> __('Current Post','acf-wp-objects'),
			'class'			=> 'code',
			'conditions'	=> [
				'field'		=> 'repeater_choices',
				'operator'	=> '==',
				'value'		=> 1
			]
		]);

	}

	/**
	 *	@filter acf/load_field/type=*
	 */
	public function migrate_field( $field ) {

		$field = wp_parse_args( $field, [
			'repeater_choices'			=> false,
			'repeater_field'			=> '',
			'repeater_label_field'		=> '', // text, num, range, url, email, date, time, datetime, image, color
			'repeater_value_field'		=> '', // text, num, range, url, email, date, time, datetime, image, color
			'repeater_post_id'			=> 0,
			'repeater_display_value'	=> 0, // media | string + media
		]);

		// migrate from boolean display value to display field
		if ( $field['repeater_choices'] ) {
			if ( $field['repeater_display_value'] ) {
				unset( $field['repeater_display_value'] );
				$field['repeater_display_field'] = $field['repeater_value_field'];
			}
		}
		return $field;
	}


	/**
	 *	@filter acf/prepare_field/type=*
	 */
	public function prepare( $field ) {

		$field = wp_parse_args( $field, [
			'repeater_choices'			=> false,
			'repeater_field'			=> '',
			'repeater_label_field'		=> '', // text, num, range, url, email, date, time, datetime, image, color
			'repeater_value_field'		=> '', // text, num, range, url, email, date, time, datetime, image, color
			'repeater_post_id'			=> 0,
			'repeater_display_field'	=> '', // media | string + media
		]);

		$post_id = $field['repeater_post_id'];

		if ( ! $post_id ) {
			global $plugin_page;
			// get current plugins page post id
			if ( isset( $plugin_page ) && ( $opt_page = acf_get_options_page( $plugin_page ) ) ) {
				$post_id = $opt_page['post_id'];
			}
		}

		if ( $field['repeater_choices'] ) {

			$raw_choices = [];
			$visuals = [];
			$classname = '-repeater-choice';
			if ( '__idx__' === $field['repeater_value_field'] ) {
				$repeater_value_field = [
					'type' => 'index',
					'is_id' => false,
				];
			} else {
				$repeater_value_field = get_field_object( $field['repeater_value_field'] );
				$repeater_value_field = wp_parse_args( $repeater_value_field, [ 'is_id' => false ] );
			}

			if ( have_rows( $field['repeater_field'], $post_id ) ) {
				while ( have_rows( $field['repeater_field'], $post_id ) ) {
					the_row();
					if ( '__idx__' ===  $field['repeater_label_field'] ) {
						$label = get_row_index() + 1;
					} else {
						$label = get_sub_field( $field['repeater_label_field'] );
					}
// var_dump($field['repeater_label_field'],get_row(false),get_sub_field( $field['repeater_label_field'] ));
					if ( '__idx__' === $field['repeater_value_field'] ) {
						$value = $label = get_row_index();
					} else {
						$value = get_sub_field( $field['repeater_value_field'] );
					}

					$visual = get_sub_field( $field['repeater_display_field'] );

					$raw_choices[ $value ] = [ 'label' => $label, 'visual' => $visual ];
				}
			}

			$raw_choices = apply_filters( 'acf_wpo_repeater_choices', $raw_choices, $field );
			$raw_choices = apply_filters( 'acf_wpo_repeater_choices/key=' . $field['repeater_field'], $raw_choices, $field );

			$choices = [];

			$display_field = acf_get_field( $field['repeater_display_field'] );

			$field_classes = explode( ' ', $field['wrapper']['class'] );
			$field_classes[] = 'repeater-choice-visualize-' . $display_field['type'];
			$field['wrapper']['class'] = implode( ' ', array_unique( $field_classes ) );

			// format choices
			foreach ( $raw_choices as $value => $choice ) {
				/* @vars $label, $visual */
				extract( $choice );
				if ( $field['repeater_display_field'] ) {
					$label = $this->get_value_display( $display_field, $label, $visual );
				}
				$key = $value;

				// value might be image object, term object, ...
				if ( is_array( $key ) ) {
					if ( isset($key['ID']) ) {
						$key = $key['ID'];
					}
				}
				// handle text fields with ID option
				if ( 'text' === $repeater_value_field['type'] && $repeater_value_field['is_id'] ) {
					$key = sanitize_title( $key );
					$key = sanitize_key( $key );
				}
				$choices[ $key ] = $label;
			}

			$field['choices'] = $choices;
			if ( ! count( $choices ) ) {
				$field['disabled'] = 1; // wont work with select fields, js is interfering
				$field['readonly'] = 1;
				$field['conditional_logic'] = [
					[ 'field' => $field['key'], 'operator' => '!=empty' ],
				]; // this condition is never true
			}
		}
		return $field;
	}

	/**
	 *	Display Value Visualization
	 *
	 *	@param array $field
	 *	@param array $label
	 *	@param array $value
	 */
	private function get_value_display( $field, $label, $value ) {

		$html = '';

		$allow_fields = apply_filters('acf_wp_objects_repeater_choices_allow_fields', $this->allow_fields );
		if ( ! isset( $allow_fields[ $field['type'] ] ) ) {
			$allow_fields[ $field['type'] ] = 'text';
		}

		$treat_as = $allow_fields[ $field['type'] ];
		switch ( $allow_fields[ $field['type'] ] ) {
			case 'image';
				$attachment = get_post( $value );
				$html = sprintf('<span class="acf-image-choice">
						%s
						<span class="image-label">%s</span>
					</span>',
					wp_get_attachment_image($value,'thumbnail', null, [ 'title' => $label ] ),
					$label
				);
				break;
			case 'color':
				$color = $match_color = $value;
				if ( empty( $value ) ) {
					$color = 'rgba(0,0,0,0)';
					$match_color = '#ffffff';
				}
				if ( empty( $label ) ) {
					$html = sprintf('
						<span class="white"></span>
						<span class="color-label" style="color:%1$s;background:%2$s;">
							<span class="screen-reader-text">%2$s</span>
						</span>',
						$this->get_matching_color( $match_color ),
						$color
					);
				} else {
					$html = sprintf('
						<span class="white"></span>
						<span class="acf-js-tooltip color-label" style="color:%1$s;background:%2$s;" title="%3$s">
							%4$s
						</span>',
						$this->get_matching_color( $match_color ),
						$color,
						esc_attr($label),
						$label
					);
				}
				break;
			case 'text':
				$html = $label;
				break;
			default:
				$html = apply_filters('acf_wp_objects_repeater_choice_label', $label, $value, $field );
		}
		return apply_filters('acf_value_display_html', $html, $value, $field );
	}

	/**
	 *	@param string $color css color string
	 *	@return string color string
	 */
	private function get_matching_color( $color ) {
		$threshold = 0.33;
		$rgba = $this->parse_color( $color );
		$a = array_pop($rgba);
		$opacity = 1 - ( array_sum($rgba) / 3 ); // 0-3
		$opacity *= $a;
		if ( $opacity > $threshold ) {
			return '#ffffff';
		}
		return '#333333';
	}

	/**
	 *	@param string $color css color string
	 *	@return array rgba
	 */
	private function parse_color( $color ) {
		$do_int = function($i) {
			return floatval($i) / 255;
		};
		$do_percent = function($i) {
			return floatval($i) / 100;
		};
		$do_hex1 = function($i) {
			return intval('0x'.$i,16) / 15;
		};
		$do_hex2 = function($i) {
			return intval('0x'.$i,16) / 255;
		};

		$a = 1;
		if ( preg_match( '/^#([0-9a-f]{1})([0-9a-f]{1})([0-9a-f]{1})$/i', $color, $matches ) ) {
			$rgb = array_map( $do_hex1, $matches );
		} else if ( preg_match( '/^#([0-9a-f]{2})([0-9a-f]{2})([0-9a-f]{2})$/i', $color, $matches ) ) {
			$rgb = array_map( $do_hex2, $matches );
		} else if ( preg_match( '/^rgb\(([\d]+),([\d]+),([\d]+)\)$/', $color, $matches ) ) {
			$rgb = array_map( $do_int, $matches );
		} else if ( preg_match( '/^rgba\(([\d]+),([\d]+),([\d]+),([\d\.]+)\)$/', $color, $matches ) ) {
			$a = array_pop( $matches );
			$rgb = array_map( $do_int, $matches );
		} else if ( preg_match( '/^rgb\(([\d%]+),([\d%]+),([\d%]+)\)$/', $color, $matches ) ) {
			$rgb = array_map( $do_percent, $matches );
		} else if ( preg_match( '/^rgba\(([\d%]+),([\d%]+),([\d\%]+),([\d\.]+)\)$/', $color, $matches ) ) {
			$a = array_pop($matches);
			$rgb = array_map( $do_percent, $matches );
		}
		$a = floatval( $a );
		$rgba = $rgb;
		$rgba[] = $a;
		return $rgba;
	}

	/**
	 *	@return array
	 */
	private function get_first_level_repeater_fields() {
		if ( is_null( $this->repeater_fields ) ) {
			$groups = [];
			$local_enabled = acf_is_local_enabled();
			if ( ! $local_enabled ) {
				acf_enable_local();
			}
			$acf_groups = acf_get_field_groups();

			foreach ( $acf_groups as $group ) {
				$fields = acf_get_fields( $group );

				if ( ! $fields ) {
					continue;
				}
				foreach ( $fields as $field ) {
					if ( $field['type'] !== 'repeater' ) {
						continue;
					}
					if ( ! isset( $groups[ $group['title'] ] ) ) {
						$groups[ $group['title'] ] = [];
					}
					$groups[ $group['title'] ][ $field['key'] ] = $field['label'];
				}

			}

			if ( ! $local_enabled ) {
				acf_disable_local();
			}
			$this->repeater_fields = $groups;
		}

		return $this->repeater_fields;
	}

	/**
	 *	@return array
	 */
	public function get_repeated_fields() {
		$repeater_groups = $this->get_first_level_repeater_fields();
		$repeated = [];
		$allow_fields = apply_filters( 'acf_wp_objects_repeater_choices_allow_fields', $this->allow_fields );
		$allow_fields = array_keys( $allow_fields );
		foreach ( $repeater_groups as $repeaters ) {
			foreach ( array_keys( $repeaters ) as $repeater_key ) {

				if ( ! $parent_field = acf_get_field( $repeater_key ) ) {
					continue;
				}

				$results = [];
				$repeated[ $parent_field['key'] ][ '__idx__' ] = __( '(Index)', 'acf-wp-objets' );
				foreach ( $parent_field['sub_fields'] as $field ) {
					if ( ! in_array( $field['type'], $allow_fields ) ) {
						continue;
					}
					if ( ! isset( $repeated[ $parent_field['key'] ] ) ) {
						$repeated[ $parent_field['key'] ] = [];
					}
					$repeated[ $parent_field['key'] ][ $field['key'] ] = $field['label'];
				}

			}
		}
		return $repeated;
	}

}
