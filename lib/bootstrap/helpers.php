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

// Prevent direct file access.
defined( 'ABSPATH' ) || die;

/**
 * Description of expected behavior.
 *
 * @since 0.1.0
 *
 * @return string
 */
function mai_is_in_dev_mode() {
	return genesis_is_in_dev_mode() || defined( 'WP_DEBUG' ) && WP_DEBUG;
}

/**
 * Description of expected behavior.
 *
 * @since 0.1.0
 *
 * @param string $needle   String to check for.
 * @param string $haystack String to check in.
 *
 * @return string
 */
function mai_has_string( $needle, $haystack ) {
	return false !== strpos( $haystack, $needle );
}

/**
 * Check if were on any type of singular page.
 *
 * @since 0.1.0
 *
 * @return bool
 */
function mai_is_type_single() {
	return is_front_page() || is_single() || is_page() || is_404() || is_attachment() || is_singular();
}

/**
 * Check if were on any type of archive page.
 *
 * @since 0.1.0
 *
 * @return bool
 */
function mai_is_type_archive() {
	return is_home() || is_post_type_archive() || is_category() || is_tag() || is_tax() || is_author() || is_date() || is_year() || is_month() || is_day() || is_time() || is_archive() || is_search();
}

/**
 * Checks if given sidebar contains a certain widget.
 *
 * @since  0.1.0
 *
 * @uses   $sidebars_widgets
 *
 * @param string $sidebar Name of sidebar, e.g `primary`.
 * @param string $widget  Widget ID to check, e.g `custom_html`.
 *
 * @return bool
 */
function mai_sidebar_has_widget( $sidebar, $widget ) {
	global $sidebars_widgets;

	if ( isset( $sidebars_widgets[ $sidebar ][0] ) && strpos( $sidebars_widgets[ $sidebar ][0], $widget ) !== false && is_active_sidebar( $sidebar ) ) {
		return true;
	}

	return false;
}

/**
 * Checks if first block is cover.
 *
 * @since 0.1.0
 *
 * @return bool
 */
function mai_has_cover_block() {
	$has_cover_block = false;

	if ( ! mai_is_type_single() || ! has_blocks() ) {
		return $has_cover_block;
	}

	$post_object = get_post( get_the_ID() );
	$blocks      = (array) parse_blocks( $post_object->post_content );

	$type  = isset( $blocks[0]['blockName'] ) ?: '';
	$align = isset( $blocks[0]['attrs']['align'] ) ?: '';

	if ( 'core/cover' === $type || 'full' === $align ) {
		$has_cover_block = true;
	}

	return $has_cover_block;
}

/**
 * Checks if current page has the page header enabled.
 *
 * @since 0.1.0
 *
 * @return bool
 */
function mai_has_page_header() {
	return in_array( 'has-page-header', get_body_class(), true );
}

/**
 * Checks if the Page Header is active.
 *
 * @since 0.1.0
 *
 * @return bool
 */
function mai_is_page_header_active() {
	$active    = false;
	$post_type = get_post_type();

	if ( mai_is_type_archive() && post_type_supports( $post_type, 'page-header-archive' ) ) {
		$active = true;
	}

	if ( mai_is_type_single() && post_type_supports( $post_type, 'page-header-single' ) ) {
		$active = true;
	}

	if ( ! $post_type && class_exists( 'WooCommerce' ) && is_shop() && post_type_supports( 'product', 'page-header-archive' ) ) {
		$active = true;
	}

	return $active;
}

/**
 * Quick and dirty way to mostly minify CSS.
 *
 * @since  1.0.0
 *
 * @author Gary Jones
 *
 * @param string $css CSS to minify.
 *
 * @return string
 */
function mai_minify_css( $css ) {
	$css = preg_replace( '/\s+/', ' ', $css );
	$css = preg_replace( '/(\s+)(\/\*(.*?)\*\/)(\s+)/', '$2', $css );
	$css = preg_replace( '~/\*(?![\!|\*])(.*?)\*/~', '', $css );
	$css = preg_replace( '/;(?=\s*})/', '', $css );
	$css = preg_replace( '/(,|:|;|\{|}|\*\/|>) /', '$1', $css );
	$css = preg_replace( '/ (,|;|\{|}|\(|\)|>)/', '$1', $css );
	$css = preg_replace( '/(:| )0\.([0-9]+)(%|em|ex|px|in|cm|mm|pt|pc)/i', '${1}.${2}${3}', $css );
	$css = preg_replace( '/(:| )(\.?)0(%|em|ex|px|in|cm|mm|pt|pc)/i', '${1}0', $css );
	$css = preg_replace( '/0 0 0 0/', '0', $css );
	$css = preg_replace( '/#([a-f0-9])\\1([a-f0-9])\\2([a-f0-9])\\3/i', '#\1\2\3', $css );

	return trim( $css );
}

/**
 * Description of expected behavior.
 *
 * @since 0.1.0
 *
 * @param string $size  Image size.
 * @param string $ratio Aspect ratio.
 *
 * @return array
 */
function mai_do_aspect_ratio( $size = 'md', $ratio = '16:9' ) {
	$ratio       = explode( ':', $ratio );
	$x           = $ratio[0];
	$y           = $ratio[1];
	$breakpoints = mai_get_breakpoints();
	$width       = isset( $breakpoints[ $size ] ) ? mai_get_breakpoint( $size ) : $size;
	$height      = (int) $width / $x * $y;

	return [ $width, $height, true ];
}
