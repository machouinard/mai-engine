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

add_action( 'acf/init', 'mai_add_hide_elements_metabox' );
/**
 * Add Hide Elements metabox.
 *
 * Location and choices added later via acf filters so
 * get_post_types() and other functions are available.
 *
 * @since 0.3.0
 *
 * @return void
 */
function mai_add_hide_elements_metabox() {
	acf_add_local_field_group(
		[
			'key'                   => 'hide_elements',
			'title'                 => __( 'Hide Elements', 'mai-engine' ),
			'menu_order'            => 10,
			'position'              => 'side',
			'label_placement'       => 'left',
			'instruction_placement' => 'label',
			'hide_on_screen'        => '',
			'active'                => true,
			'description'           => '',
			'location'              => [
				[
					[
						'param'    => 'mai_public_post_type',
						'operator' => '==', // Currently unused.
						'value'    => true, // Currently unused.
					]
				]
			],
			'fields'                => [
				[
					'key'           => 'hide_elements',
					'name'          => 'hide_elements',
					'type'          => 'checkbox',
					'instructions'  => __( 'Select elements to hide on this page.', 'mai-engine' ),
					'default_value' => [],
					'choices'       => [],
				],
			],
		]
	);
}

add_filter( 'acf/location/rule_match/mai_public_post_type', 'mai_acf_public_post_type_rule_match', 10, 4 );
/**
 * Show "Hide Elements" metabox on all public post types.
 *
 * @since 0.3.12
 *
 * @param bool   $result      Whether the rule matches.
 * @param array  $rule        The current rule you're matching. Includes 'param', 'operator' and 'value' parameters.
 * @param string $screen      The current screen.
 * @param array  $field_group Data about the current edit screen (post_id, page_template...).
 *
 * @return bool $match
 */
function mai_acf_public_post_type_rule_match( $result, $rule, $screen, $field_group ) {
	return isset( $screen['post_type'] ) && in_array( $screen['post_type'], get_post_types( [ 'public' => true ] ) );
}

add_filter( 'acf/load_field/key=hide_elements', 'mai_load_hide_elements_field' );
/**
 * Load "Hide Elements" metabox choices.
 *
 * @since 0.3.3
 *
 * @param array $field The existing field array.
 *
 * @return array
 */
function mai_load_hide_elements_field( $field ) {

	$field['choices'] = [];
	$post_type        = mai_get_admin_post_type();
	$page_header      = mai_get_option( 'page-header-single', mai_get_config( 'page-header' )['single'] );

	if ( is_active_sidebar( 'before-header' ) ) {
		$field['choices']['before_header'] = __( 'Before Header', 'mai-engine' );
	}

	$field['choices']['site_header'] = __( 'Site Header', 'mai-engine' );

	if ( mai_get_option( 'site-header-sticky', current_theme_supports( 'sticky-header' ) ) ) {
		$field['choices']['sticky_header'] = __( 'Sticky Header', 'mai-engine' );
	}

	if ( mai_get_option( 'site-header-transparent', current_theme_supports( 'transparent-header' ) ) ) {
		$field['choices']['transparent_header'] = __( 'Transparent Header', 'mai-engine' );
	}

	if ( has_nav_menu( 'after-header' ) ) {
		$field['choices']['after_header'] = __( 'After Header Menu', 'mai-engine' );
	}

	if ( $page_header &&  ( '*' === $page_header || ( is_array( $page_header ) && in_array( $post_type, $page_header ) ) ) ) {
		$field['choices']['page_header'] = __( 'Page Header', 'mai-engine' );
	}

	if ( ( 'page' === $post_type && genesis_get_option( 'breadcrumb_page' ) ) || (  'page' !== $post_type && genesis_get_option( 'breadcrumb_single' ) ) ) {
		$field['choices']['breadcrumbs'] = __( 'Breadcrumbs', 'mai-engine' );
	}

	$field['choices']['entry_title']    = __( 'Entry Title', 'mai-engine' );
	$field['choices']['entry_excerpt']  = __( 'Entry Excerpt', 'mai-engine' );
	$field['choices']['featured_image'] = __( 'Featured Image', 'mai-engine' );

	if ( is_active_sidebar( 'before-footer' ) ) {
		$field['choices']['before_footer'] = __( 'Before Footer', 'mai-engine' );
	}

	if ( is_active_sidebar( 'footer' ) ) {
		$field['choices']['footer'] = __( 'Footer', 'mai-engine' );
	}

	if ( ( is_active_sidebar( 'footer-credits' ) || mai_get_widget_area_default_content( 'footer-credits' ) ) ) {
		$field['choices']['footer_credits'] = __( 'Footer Credits', 'mai-engine' );
	}

	return $field;
}
