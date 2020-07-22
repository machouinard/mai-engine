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

add_filter( 'render_block', 'mai_render_group_block', 10, 2 );
/**
 * Customizes group block HTML output.
 *
 * @since 2.0.1
 *
 * @param string $block_content The existing block content.
 * @param array  $block         The button block object.
 *
 * @return string
 */
function mai_render_group_block( $block_content, $block ) {
	if ( 'core/group' !== $block['blockName'] ) {
		return $block_content;
	}

	$light_or_dark = false;

	if ( isset( $block['attrs']['backgroundColor'] ) ) {
		$light_or_dark = mai_is_light_color( $block['attrs']['backgroundColor'] ) ? 'light' : 'dark';
	} elseif ( isset( $block['attrs']['customBackgroundColor'] ) ) {
		$light_or_dark = mai_is_light_color( $block['attrs']['customBackgroundColor'] ) ? 'light' : 'dark';
	}

	if ( $light_or_dark ) {

		$dom = mai_get_dom_document( $block_content );

		/**
		 * @var DOMElement $first_block The group block container.
		 */
		$first_block = $dom->childNodes && isset( $dom->childNodes[0] ) ? $dom->childNodes[0] : false;

		if ( $first_block ) {
			$classes = $first_block->getAttribute( 'class' );
			$classes = mai_add_classes( sprintf( 'has-%s-background', $light_or_dark ), $classes );

			$first_block->setAttribute( 'class', $classes );

			$block_content = $dom->saveHTML();
		}
	}

	return $block_content;
}
