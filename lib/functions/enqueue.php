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

// Genesis style trump.
remove_action( 'genesis_meta', 'genesis_load_stylesheet' );
add_action( 'wp_enqueue_scripts', 'genesis_enqueue_main_stylesheet', 99 );

add_action( 'wp_enqueue_scripts', 'mai_enqueue_assets' );
add_action( 'enqueue_block_editor_assets', 'mai_enqueue_assets' );
/**
 * Register and enqueue all scripts and styles.
 *
 * @since 0.1.0
 *
 * @return void
 */
function mai_enqueue_assets() {
	$assets       = mai_config( 'scripts-and-styles' )['add'];
	$google_fonts = implode( '|', mai_config( 'google-fonts' ) );

	if ( $google_fonts ) {
		$assets[] = [
			'handle' => mai_handle() . '-google-fonts',
			'src'    => "//fonts.googleapis.com/css?family=$google_fonts&display=swap",
			'editor' => 'both',
		];
	}

	foreach ( $assets as $asset ) {
		$handle    = $asset['handle'];
		$src       = isset( $asset['src'] ) ? $asset['src'] : '';
		$type      = false !== strpos( $src, '.js' ) ? 'script' : 'style';
		$deps      = isset( $asset['deps'] ) ? $asset['deps'] : [];
		$ver       = isset( $asset['ver'] ) ? $asset['ver'] : genesis_get_theme_version();
		$media     = isset( $asset['media'] ) ? $asset['media'] : 'all';
		$in_footer = isset( $asset['in_footer'] ) ? $asset['in_footer'] : true;
		$editor    = isset( $asset['editor'] ) ? $asset['editor'] : false;
		$condition = isset( $asset['condition'] ) ? $asset['condition'] : '__return_true';
		$localize  = isset( $asset['localize'] ) ? $asset['localize'] : [];
		$last_arg  = 'style' === $type ? $media : $in_footer;
		$register  = "wp_register_$type";
		$enqueue   = "wp_enqueue_$type";

		if ( is_admin() && $editor || ! is_admin() && ! $editor || 'both' === $editor ) {
			if ( is_callable( $condition ) && $condition() ) {
				$register( $handle, $src, $deps, $ver, $last_arg );
				$enqueue( $handle );

				if ( ! empty( $localize ) ) {
					wp_localize_script( $handle, $localize['name'], $localize['data'] );
				}
			}
		}
	}
}

add_action( 'wp_enqueue_scripts', 'mai_deregister_scripts_and_styles', 15 );
/**
 * Deregister scripts.
 *
 * @since 0.1.0
 *
 * @return void
 */
function mai_deregister_scripts_and_styles() {
	$assets = mai_config( 'scripts-and-styles' )['remove'];

	foreach ( $assets as $asset ) {
		wp_deregister_script( $asset );
		wp_deregister_style( $asset );
	}
}
