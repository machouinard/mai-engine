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

add_action( 'admin_init', 'mai_plugin_updater' );
/**
 * Description of expected behavior.
 *
 * @since 0.1.0
 *
 * @return void
 */
function mai_plugin_updater() {
	// Bail if current user cannot manage plugins.
	if ( ! current_user_can( 'install_plugins' ) ) {
		return;
	}
	Puc_v4_Factory::buildUpdateChecker(
		'https://github.com/maithemewp/mai-engine',
		__FILE__,
		'mai-engine'
	);
}
