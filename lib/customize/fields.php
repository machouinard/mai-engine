<?php
/**
 * Mai Engine.
 *
 * @package   BizBudding\MaiEngine
 * @link      https://bizbudding.com
 * @author    BizBudding
 * @copyright Copyright © 2019 BizBudding
 * @license   GPL-2.0-or-later
 */

/**
 * Setup the field data from config for kirki add_field method.
 *
 * @param  array  $field      The field config data.
 * @param  string $section_id The Customizer section ID.
 * @param  string $name       The post or content type name.
 *
 * @return array The field data.
 */
function mai_get_kirki_field_data( $field, $section_id, $name = '' ) {
	$data = [
		'type'        => $field['type'],
		'label'       => $field['label'],
		'settings'    => $field['name'],
		'section'     => $section_id,
		'priority'    => 10,
		'option_type' => 'option',
		'option_name' => sprintf(
			'%s[%s]',
			mai_get_handle(),
			str_replace( mai_get_handle() . '-', '', $section_id )
		),
	];

	// Maybe add description.
	if ( isset( $field['desc'] ) ) {
		$data['description'] = $field['desc'];
	}

	// Maybe add attributes.
	if ( isset( $field['atts'] ) ) {
		foreach ( $field['atts'] as $key => $value ) {
			$data[ $key ] = $value;
		}
	}

	// Maybe add conditional logic.
	if ( isset( $field['conditions'] ) ) {
		foreach ( $field['conditions'] as $index => $condition ) {
			foreach ( $condition as $key => $value ) {
				if ( 'setting' === $key ) {
					$field['conditions'][ $index ][ $key ] = "{$data['option_name']}[$value]";
				}
			}
		}

		$data['active_callback'] = $field['conditions'];
	}

	// Maybe add default.
	if ( isset( $field['default'] ) ) {
		if ( is_array( $field['default'] ) ) {
			$data['default'] = $field['default'];
		} elseif ( is_string( $field['default'] ) && is_callable( $field['default'] ) && mai_has_string( 'mai_', $field['default'] ) ) {
			$data['default'] = call_user_func_array( $field['default'], [ 'name' => $name ] );
		} else {
			$data['default'] = $field['default'];
		}
	}

	// Maybe get choices.
	if ( isset( $field['choices'] ) ) {
		if ( is_array( $field['choices'] ) ) {
			$data['choices'] = $field['choices'];
		} elseif ( is_callable( $field['choices'] ) && mai_has_string( 'mai_', $field['choices'] ) ) {
			$data['choices'] = call_user_func_array( $field['choices'], [ 'name' => $name ] );
		}
	}

	// Maybe add custom sanitize function.
	if ( isset( $field['sanitize'] ) ) {
		$data['sanitize_callback'] = $field['sanitize'];
	}

	return $data;
}
