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
 * Returns the plugin directory.
 *
 * @since 0.1.0
 *
 * @return string
 */
function mai_get_dir() {
	static $dir = null;

	if ( is_null( $dir ) ) {
		$dir = trailingslashit( dirname( dirname( __DIR__ ) ) );
	}

	return $dir;
}

/**
 * Returns the plugin URL.
 *
 * @since 0.1.0
 *
 * @return string
 */
function mai_get_url() {
	static $url = null;

	if ( is_null( $url ) ) {
		$url = trailingslashit( plugins_url( basename( mai_get_dir() ) ) );
	}

	return $url;
}

/**
 * Returns an array of plugin data from the main plugin file.
 *
 * @since 0.1.0
 *
 * @param string $key Optionally return one key.
 *
 * @return array|string|null
 */
function mai_get_plugin_data( $key = '' ) {
	static $data = null;

	if ( is_null( $data ) ) {
		$data = get_file_data(
			mai_get_dir() . 'mai-engine.php',
			[
				'name'        => 'Plugin Name',
				'version'     => 'Version',
				'db-version'  => 'DB Version',
				'plugin-uri'  => 'Plugin URI',
				'text-domain' => 'Text Domain',
				'description' => 'Description',
				'author'      => 'Author',
				'author-uri'  => 'Author URI',
				'domain-path' => 'Domain Path',
				'network'     => 'Network',
			],
			'plugin'
		);
	}

	if ( array_key_exists( $key, $data ) ) {
		return $data[ $key ];
	}

	return $data;
}

/**
 * Returns the plugin name.
 *
 * @since 0.1.0
 *
 * @return string
 */
function mai_get_name() {
	static $name = null;

	if ( is_null( $name ) ) {
		$name = mai_get_plugin_data( 'name' );
	}

	return $name;
}

/**
 * Returns the plugin handle/text domain.
 *
 * @since 0.1.0
 *
 * @return string
 */
function mai_get_handle() {
	static $handle = null;

	if ( is_null( $handle ) ) {
		$handle = mai_get_plugin_data( 'text-domain' );
	}

	return $handle;
}

/**
 * Returns the plugin version.
 *
 * @since 0.1.0
 *
 * @return string
 */
function mai_get_version() {
	static $version = null;

	if ( is_null( $version ) ) {
		$version = mai_get_plugin_data( 'version' );
	}

	return $version;
}

/**
 * Description of expected behavior.
 *
 * @since 0.1.0
 *
 * @param string $file File path.
 *
 * @return string
 */
function mai_get_asset_version( $file ) {
	$file    = str_replace( mai_get_url(), mai_get_dir(), $file );
	$version = mai_get_version();
	if ( file_exists( $file ) && mai_has_string( mai_get_dir(), $file ) ) {
		$version .= '.' . date( 'njYHi', filemtime( $file ) );
	}

	return $version;
}

/**
 * Description of expected behavior.
 *
 * @since 0.1.0
 *
 * @param string $file File base name.
 *
 * @return string
 */
function mai_get_asset_url( $file ) {
	$type    = false !== strpos( $file, '.js' ) ? 'js' : 'css';
	$name    = str_replace( [ '.js', '.css' ], '', $file );
	$uri     = mai_get_url();
	$default = "${uri}assets/${type}/${name}.${type}";
	$min     = "${uri}assets/${type}/min/${name}.min.${type}";

	return mai_is_in_dev_mode() ? $default : $min;
}

/**
 * Returns the active child theme's config.
 *
 * @since 0.1.0
 *
 * @param string $sub_config Name of config to get.
 *
 * @return array
 */
function mai_get_config( $sub_config = 'default' ) {
	$config = require mai_get_dir() . 'config/_default/config.php';
	$theme  = mai_get_dir() . 'config/' . mai_get_active_theme() . '/config.php';

	if ( is_readable( $theme ) ) {
		$config = array_replace_recursive( $config, require $theme );
	}

	// Allow users to override from within actual child theme.
	$child = get_stylesheet_directory() . '/config.php';

	if ( is_readable( $child ) ) {
		$config = array_replace_recursive( $config, require $child );
	}

	$configs[ $sub_config ] = isset( $config[ $sub_config ] ) ? $config[ $sub_config ] : [];

	return apply_filters( "mai_{$sub_config}_config", $configs[ $sub_config ] );
}

/**
 * Returns the active theme key.
 *
 * Checks multiple places to find a match.
 *
 * @since 0.1.0
 *
 * @return string
 */
function mai_get_active_theme() {
	static $theme = null;

	if ( is_null( $theme ) ) {
		if ( ! $theme ) {
			$theme = get_theme_support( mai_get_handle() )[0];
		}

		if ( ! $theme ) {
			$theme = genesis_get_theme_handle();
		}

		if ( ! $theme ) {
			$theme = wp_get_theme()->get( 'TextDomain' );
		}

		$theme = str_replace( 'mai-', '', $theme );

		if ( ! $theme || ! in_array( $theme, mai_get_child_themes(), true ) ) {
			$theme = 'default';
		}
	}

	return $theme;
}

/**
 * Returns an array of all BizBudding child themes.
 *
 * @since 0.1.0
 *
 * @return array
 */
function mai_get_child_themes() {
	$child_themes = [];
	$files        = glob( mai_get_dir() . 'config/*', GLOB_ONLYDIR );

	foreach ( $files as $file ) {
		$child_themes[] = basename( $file, '.php' );
	}

	return $child_themes;
}

/**
 * Description of expected behavior.
 *
 * @since 0.1.0
 *
 * @return array
 */
function mai_get_options() {
	$handle = mai_get_handle();

	if ( is_customize_preview() ) {
		$options = get_option( $handle );
	} else {
		static $options = [];

		if ( empty( $options ) ) {
			$options = get_option( $handle );
		}
	}

	return $options;
}

/**
 * Description of expected behavior.
 *
 * @since 0.1.0
 *
 * @param string $option  Option name.
 * @param mixed  $default Default value.
 *
 * @return mixed
 */
function mai_get_option( $option, $default = false ) {
	$options = mai_get_options();

	return isset( $options[ $option ] ) ? $options[ $option ] : $default;
}

/**
 * Description of expected behavior.
 *
 * @since 1.0.0
 *
 * @param string $option Option name.
 * @param mixed  $value  Option value.
 *
 * @return void
 */
function mai_update_option( $option, $value ) {
	$handle = mai_get_handle();

	// Can't be static.
	$options = get_option( $handle );

	$options[ $option ] = $value;

	update_option( $handle, $options );
}

/**
 * Description of expected behavior.
 *
 * @since 1.0.0
 *
 * @param string $name Settings to get.
 *
 * @return mixed
 */
function mai_get_settings( $name ) {
	return require mai_get_dir() . 'config/_settings/' . $name . '.php';
}

/**
 * Returns an array of the themes JSON variables.
 *
 * @since 0.1.0
 *
 * @return array
 */
function mai_get_variables() {
	static $variables;

	if ( is_null( $variables ) ) {
		// phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents
		$defaults = json_decode( file_get_contents( mai_get_dir() . 'config/_default/config.json' ), true );
		$file     = mai_get_dir() . 'config/' . mai_get_active_theme() . '/config.json';
		// phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents
		$theme     = is_readable( $file ) ? json_decode( file_get_contents( $file ), true ) : [];
		$variables = array_replace_recursive( $defaults, $theme );
	}

	return $variables;
}

/**
 * Description of expected behavior.
 *
 * @since 0.1.0
 *
 * @return array
 */
function mai_get_colors() {
	static $colors = [];

	if ( empty( $colors ) ) {
		$colors = mai_get_variables()['colors'];

		foreach ( $colors as $name => $hex ) {
			$colors[ $name ] = $hex;
		}
	}

	return $colors;
}

/**
 * Description of expected behavior.
 *
 * @since 0.1.0
 *
 * @param string $color Name of the color to get.
 *
 * @return string
 */
function mai_get_color( $color = null ) {
	$colors = mai_get_colors();

	return isset( $colors[ $color ] ) ? $colors[ $color ] : '';
}

/**
 * Returns the color palette variables.
 *
 * @since 0.1.0
 *
 * @return array
 */
function mai_get_color_palette() {
	$colors  = mai_get_colors();
	$option  = mai_get_option( 'global-color-palette', [] );
	$palette = [];

	foreach ( $colors as $name => $hex ) {
		$palette[] = [
			'name'  => mai_convert_case( $name, 'title' ),
			'slug'  => mai_convert_case( $name, 'kebab' ),
			'color' => isset( $option[ $name ] ) ? $option[ $name ] : $hex,
		];
	}

	return $palette;
}

/**
 * Description of expected behavior.
 *
 * @since 0.1.0
 *
 * @return array
 */
function mai_get_breakpoints() {
	static $breakpoints = [];

	if ( empty( $breakpoints ) ) {
		$breakpoint        = mai_get_variables()['breakpoint'];
		$breakpoints['xs'] = absint( $breakpoint / 3 );   // 400  (400 x 1)
		$breakpoints['sm'] = absint( $breakpoint / 2 );   // 600  (400 x 1.5)
		$breakpoints['md'] = absint( $breakpoint / 1.5 ); // 800  (400 x 2)
		$breakpoints['lg'] = absint( $breakpoint / 1.2 ); // 1000 (400 x 2.5)
		$breakpoints['xl'] = absint( $breakpoint / 1 );   // 1200 (400 x 3)
	}

	return $breakpoints;
}

/**
 * Returns the default breakpoint for the theme.
 *
 * @since 0.1.0
 *
 * @param string $size   Breakpoint size.
 * @param string $suffix Optional suffix, e.g. 'px'.
 *
 * @return mixed
 */
function mai_get_breakpoint( $size = 'lg', $suffix = '' ) {
	$breakpoints = mai_get_breakpoints();

	return mai_get_option( 'breakpoint', $breakpoints[ $size ] . $suffix );
}

/**
 * Return the current post type.
 * Sometimes we need this earlier than get_post_type()
 * can handle, so we fall back to the query var.
 *
 * @since 0.1.0
 *
 * @return string
 */
function mai_get_post_type() {
	$name = get_post_type();

	return $name ?: get_query_var( 'post_type' );
}

/**
 * Get the unit value.
 *
 * If only a number value, use the fallback..
 *
 * @since 0.1.0
 *
 * @param  string $value    The value. Could be integer 24 or with type 24px, 2rem, etc.
 * @param  string $fallback The fallback unit value.
 *
 * @return string
 */
function mai_get_unit_value( $value, $fallback = 'px' ) {
	if ( empty( $value ) || is_numeric( $value ) ) {
		return sprintf( '%s%s', intval( $value ), $fallback );
	}

	return trim( $value );
}

/**
 * Description of expected behavior.
 *
 * @since 1.0.0
 *
 * @param $string
 *
 * @return int
 */
function mai_get_integer_value( $string ) {
	return (int) preg_replace( "/[^0-9.]/", "", $string );
}

/**
 * Description of expected behavior.
 *
 * @since 1.0.0
 *
 * @return array
 */
function mai_get_site_layout_choices() {
	return [ '' => esc_html__( 'Site Default', 'mai-engine' ) ] + genesis_get_layouts_for_customizer();
}

/**
 * Get the reusable block content by ID.
 *
 * @since 0.2.0
 *
 * @param int|string $post_slug_or_id The reusable block wp_block post slug or ID.
 *
 * @return string|HTML
 */
function mai_get_reusable_block( $post_slug_or_id ) {
	$content = false;
	if ( is_numeric( $post_slug_or_id ) ) {
		$content = get_post_field( 'post_content', $post_slug_or_id );
	} else {
		$post = get_page_by_path( $post_slug_or_id, OBJECT, 'wp_block' );
		if ( $post ) {
			$content = $post->post_content;
		}
	}
	if ( ! $content ) {
		return;
	}
	// TODO: Do we want to apply ALL the_content filters here?
	return apply_filters( 'the_content', $content );
}

/**
 * Description of expected behavior.
 *
 * @since 1.0.0
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

	$svg = mai_get_svg( $args['icon'], $args['style'] );

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
	$attributes['style'] .= sprintf( '--icon-size:%s;', mai_get_unit_value( $args['size'] ) );
	$attributes['style'] .= sprintf( '--icon-color:%s;', $args['color_icon'] );
	$attributes['style'] .= sprintf( '--icon-margin:%s %s %s %s;', mai_get_unit_value( $args['margin_top'] ), mai_get_unit_value( $args['margin_right'] ), mai_get_unit_value( $args['margin_bottom'] ), mai_get_unit_value( $args['margin_left'] ) );
	$attributes['style'] .= sprintf( '--icon-padding:%s;', mai_get_unit_value( $args['padding'] ) );

	if ( $args['color_background'] ) {
		$attributes['style'] .= sprintf( '--icon-background:%s;', $args['color_background'] );
	}

	if ( $args['color_shadow'] ) {
		$attributes['style'] .= sprintf( '--icon-box-shadow:%s %s %s %s;', mai_get_unit_value( $args['x_offset'] ), mai_get_unit_value( $args['y_offset'] ), mai_get_unit_value( $args['blur'] ), $args['color_shadow'] );
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
		'style'            => 'light',
		'icon'             => 'bolt',
		'display'          => 'flex',
		'align'            => 'center',
		'size'             => '40',
		'class'            => '',
		'color_icon'       => 'currentColor',
		'color_background' => '',
		'color_border'     => '',
		'color_shadow'     => '',
		'margin_top'       => 0,
		'margin_right'     => 0,
		'margin_left'      => 0,
		'margin_bottom'    => 0,
		'padding'          => 0,
		'border_width'     => 0,
		'border_radius'    => '50%',
		'x_offset'         => 0,
		'y_offset'         => 0,
		'blur'             => 0,
	];
}

/**
 * Description of expected behavior.
 *
 * @since 0.1.0
 *
 * @param string $name  SVG name.
 * @param string $style SVG style.
 * @param string $class SVG classes.
 *
 * @return string
 */
function mai_get_svg( $name, $style = 'light', $class = '' ) {
	$file = mai_get_dir() . "assets/icons/svgs/$style/$name.svg";

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
 * Description of expected behavior.
 *
 * @since 0.1.0
 *
 * @param string $name  SVG name.
 * @param string $style SVG style.
 *
 * @return string
 */
function mai_get_svg_url( $name, $style = 'light' ) {
	return mai_get_url() . "assets/icons/svgs/$style/$name.svg";
}

/**
 * Description of expected behavior.
 *
 * @since 0.1.0
 *
 * @return array
 */
function mai_get_editor_localized_data() {
	$palette = mai_get_color_palette();
	$palette = wp_list_pluck( $palette, 'color', 'slug' );

	unset( $palette['black'] ); // Too many for iris picker, we need to remove some.
	unset( $palette['white'] );
	unset( $palette['light'] );

	$palette  = array_values( $palette ); // Remove keys.
	$data     = [ 'palette' => $palette ];
	$settings = mai_get_settings( 'grid-block' );

	foreach ( $settings as $key => $field ) {
		if ( 'tab' === $field['type'] ) {
			continue;
		}

		foreach ( [ 'post', 'term', 'user' ] as $type ) {
			if ( ! in_array( $type, $field['block'], true ) ) {
				continue;
			}
			if ( isset( $field['atts']['sub_fields'] ) ) {
				foreach ( $field['atts']['sub_fields'] as $sub_key => $sub_field ) {
					$data[ $type ][ $sub_field['name'] ] = $sub_key;
				}
			} else {
				$data[ $type ][ $field['name'] ] = $key;
			}
		}
	}

	return $data;
}
