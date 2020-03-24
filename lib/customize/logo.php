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

add_action( 'init', 'mai_logo_customizer_settings' );
/**
 * Add logo customizer settings.
 *
 * @return  void
 */
function mai_logo_customizer_settings() {

	// Bail if no Kirki.
	if ( ! class_exists( 'Kirki' ) ) {
		return;
	}

	$config_id = 'mai_header';

	/**
	 * Kirki Config.
	 */
	Kirki::add_config(
		$config_id,
		[
			'capability'  => 'edit_theme_options',
			'option_type' => 'option',
			'option_name' => $config_id,
		]
	);


	Kirki::add_field(
		$config_id,
		[
			'type'     => 'dimensions',
			'settings' => 'logo_width',
			'label'    => esc_html__( 'Logo Width', 'mai-engine' ),
			'section'  => 'title_tagline',
			'priority' => 60,
			'default'  => [
				'desktop' => '180px',
				'mobile'  => '120px',
			],
			'choices'  => [
				'labels' => [
					'desktop' => esc_html__( 'Desktop', 'mai-engine' ),
					'mobile'  => current_theme_supports( 'sticky-header' ) ? esc_html__( 'Mobile / Sticky', 'mai-engine' ) : esc_html__( 'Mobile', 'mai-engine' ),
				],
			],
			'output'   => [
				[
					'choice'   => 'mobile',
					'element'  => [ ':root', '.is-stuck' ],
					'property' => '--custom-logo-width',
				],
				[
					'choice'      => 'desktop',
					'element'     => ':root',
					'property'    => '--custom-logo-width',
					'media_query' => sprintf( '@media (min-width: %spx)', mai_get_breakpoint( 'lg' ) ),
				],
			],
		]
	);

	Kirki::add_field(
		$config_id,
		[
			'type'     => 'dimensions',
			'settings' => 'logo_spacing',
			'label'    => esc_html__( 'Logo Spacing', 'mai-engine' ),
			'section'  => 'title_tagline',
			'priority' => 60,
			'default'  => [
				'desktop' => '36px',
				'mobile'  => '16px',
			],
			'choices'  => [
				'labels' => [
					'desktop' => esc_html__( 'Desktop', 'mai-engine' ),
					'mobile'  => current_theme_supports( 'sticky-header' ) ? esc_html__( 'Mobile / Sticky', 'mai-engine' ) : esc_html__( 'Mobile', 'mai-engine' ),
				],
			],
			'output'   => [
				[
					'choice'   => 'mobile',
					'element'  => [ ':root', '.is-stuck' ],
					'property' => '--title-area-padding',
				],
				[
					'choice'      => 'desktop',
					'element'     => ':root',
					'property'    => '--title-area-padding',
					'media_query' => sprintf( '@media (min-width: %spx)', mai_get_breakpoint( 'lg' ) ),
				],
			],
		]
	);
}
