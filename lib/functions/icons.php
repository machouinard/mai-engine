<?php
/**
 * Mai Engine.
 *
 * @package   BizBudding\MaiEngine
 * @link      https://bizbudding.com
 * @author    BizBudding
 * @copyright Copyright © 2020 BizBudding
 * @license   GPL-2.0-or-later
 */

// Prevent direct file access.
defined( 'ABSPATH' ) || die;

/**
 * Description of expected behavior.
 *
 * @since 0.1.0
 *
 * @param array $args Icon args.
 *
 * @return null|string
 */
function mai_get_icon( $args ) {
	static $id = 0;

	$id++;

	$args = shortcode_atts(
		mai_get_icon_default_args(),
		$args,
		'mai_icon'
	);

	$args = array_map(
		'esc_html',
		$args
	);

	$svg = mai_get_svg_icon( $args['icon'], $args['style'] );

	if ( ! $svg ) {
		return '';
	}

	// Build classes.
	$class = sprintf( 'mai-icon mai-icon-%s', $id );

	// Add custom classes.
	if ( ! empty( $args['class'] ) ) {
		$class .= ' ' . esc_attr( $args['class'] );
	}

	// Get it started.
	$attributes = [
		'class' => $class,
		'style' => '',
	];

	// Build inline styles.
	$attributes['style'] .= sprintf( '--icon-display:%s;', $args['display'] );
	$attributes['style'] .= sprintf( '--icon-align:%s;', $args['align'] );
	$attributes['style'] .= sprintf( '--icon-margin:%s %s %s %s;', mai_get_unit_value( $args['margin_top'] ), mai_get_unit_value( $args['margin_right'] ), mai_get_unit_value( $args['margin_bottom'] ), mai_get_unit_value( $args['margin_left'] ) );
	$attributes['style'] .= sprintf( '--icon-padding:%s;', mai_get_unit_value( $args['padding'] ) );

	if ( $args['size'] ) {
		$attributes['style'] .= sprintf( '--icon-size:%s;', mai_get_unit_value( $args['size'] ) );
	}

	if ( $args['color_icon'] ) {
		$attributes['style'] .= sprintf( '--icon-color:%s;', $args['color_icon'] );
	}

	if ( $args['color_background'] ) {
		$attributes['style'] .= sprintf( '--icon-background:%s;', $args['color_background'] );
	}

	if ( $args['color_shadow'] ) {
		$attributes['style'] .= sprintf( '--icon-box-shadow:%s %s %s %s;', mai_get_unit_value( $args['x_offset'] ), mai_get_unit_value( $args['y_offset'] ), mai_get_unit_value( $args['blur'] ), $args['color_shadow'] );
	}

	if ( $args['color_text_shadow'] ) {
		$attributes['style'] .= sprintf( '--icon-text-shadow:%s %s %s %s;', mai_get_unit_value( $args['text_shadow_x_offset'] ), mai_get_unit_value( $args['text_shadow_y_offset'] ), mai_get_unit_value( $args['text_shadow_blur'] ), $args['color_text_shadow'] );
	}

	if ( $args['border_width'] && $args['color_border'] ) {
		$attributes['style'] .= sprintf( '--icon-border:%s solid %s;', mai_get_unit_value( $args['border_width'] ), mai_get_unit_value( $args['color_border'] ) );
	}

	if ( $args['border_radius'] ) {
		$radius              = explode( ' ', trim( $args['border_radius'] ) );
		$radius              = array_map( 'mai_get_unit_value', $radius );
		$radius              = array_filter( $radius );
		$attributes['style'] .= sprintf( '--icon-border-radius:%s;', implode( ' ', $radius ) );
	}

	return genesis_markup(
		[
			'open'    => '<span %s><span class="mai-icon-wrap">',
			'close'   => '</span></span>',
			'content' => $svg,
			'context' => 'mai-icon',
			'echo'    => false,
			'atts'    => $attributes,
		]
	);
}

/**
 * Helper function that returns list of shortcode attributes.
 *
 * @since 0.1.0
 *
 * @return array
 */
function mai_get_icon_default_args() {
	return [
		'style'                => 'light',
		'icon'                 => 'bolt',
		'icon_brand'           => 'wordpress-simple',
		'display'              => 'block',
		'align'                => 'center',
		'size'                 => '40',
		'class'                => '',
		'color_icon'           => 'currentColor',
		'color_background'     => '',
		'color_border'         => '',
		'color_shadow'         => '',
		'color_text_shadow'    => '',
		'margin_top'           => 0,
		'margin_right'         => 0,
		'margin_left'          => 0,
		'margin_bottom'        => 0,
		'padding'              => 0,
		'border_width'         => 0,
		'border_radius'        => '50%',
		'x_offset'             => 0,
		'y_offset'             => 0,
		'blur'                 => 0,
		'text_shadow_x_offset' => 0,
		'text_shadow_y_offset' => 0,
		'text_shadow_blur'     => 0,
	];
}

/**
 * Description of expected behavior.
 *
 * @since 0.2.0
 *
 * @param string $name  SVG name.
 * @param string $class SVG class name.
 *
 * @return string
 */
function mai_get_svg( $name, $class = '' ) {
	$file = mai_get_dir() . "assets/svg/$name.svg";

	if ( ! file_exists( $file ) ) {
		return '';
	}

	$svg = file_get_contents( $file );

	if ( $class ) {
		$svg = str_replace( '<svg', "<svg class='$class' ", $svg );
	}

	// phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents
	return $svg;
}

/**
 * Returns an SVG string.
 *
 * @since 2.3.1 Added check for dom element.
 * @since 0.1.0
 *
 * @param string $name  SVG name.
 * @param string $style SVG style.
 * @param array  $atts  SVG HTML attributes.
 *
 * @return string
 */
function mai_get_svg_icon( $name, $style = 'light', $atts = [] ) {
	$file = mai_get_dir() . "assets/icons/svgs/$style/$name.svg";

	if ( ! file_exists( $file ) ) {
		return '';
	}

	$svg = file_get_contents( $file );

	if ( $atts ) {
		$dom  = mai_get_dom_document( $svg );
		$svgs = $dom->getElementsByTagName( 'svg' );

		foreach ( $atts as $att => $value ) {

			/**
			 * @var DOMElement $first_svg First dom element.
			 */
			$first_svg = isset( $svgs[0] ) ? $svgs[0] : null;

			if ( $first_svg ) {
				$first_svg->setAttribute( $att, $value );
			}
		}

		$svg = $dom->saveHTML();
	}

	// phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents
	return $svg;
}

/**
 * Description of expected behavior.
 *
 * @since 0.1.0
 *
 * @param string $name  SVG name.
 * @param string $style SVG style.
 *
 * @return string
 */
function mai_get_svg_icon_url( $name, $style = 'light' ) {
	return mai_get_url() . "assets/icons/svgs/$style/$name.svg";
}
