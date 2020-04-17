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

return [
	'google-fonts'     => [
		// TODO: Do we need all of the Work Sans variants? (No, not sure which weights we're using yet so have them all in there - Mike).
		'Josefin+Sans:600,700|Work+Sans:400,400i,500,500i,600,600i,700,700i',
	],
	'image-sizes'      => [
		'add' => [
			'portrait' => '3:4',
		],
	],
	'theme-support'    => [
		'add' => [
			'boxed-container',
			'sticky-header',
		],
	],
	'page-header'      => [
		'single' => [ 'page' ],
	],
	'required-plugins' => [
		[
			'name'     => 'Simple Social Icons',
			'host'     => 'wordpress',
			'slug'     => 'simple-social-icons/simple-social-icons.php',
			'uri'      => 'https://wordpress.org/plugins/simple-social-icons/',
			'optional' => true,
		],
		[
			'name'     => 'WP Forms Lite',
			'host'     => 'wordpress',
			'slug'     => 'wpforms-lite/wpforms.php',
			'uri'      => 'https://wordpress.org/plugins/query-monitor/',
			'optional' => true,
		],
	],
];
