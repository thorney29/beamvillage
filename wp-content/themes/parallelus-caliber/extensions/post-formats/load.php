<?php
/*
	Extension Name: Post Formats
	Extension URI:
	Version: 1.0.2
	Description: User interface and controls for post formats.
	Author: Parallelus
	Author URI: http://runwaywp.com
	Text Domain:
	Domain Path:
	Network:
	Site Wide Only:
*/

// Settings
$fields  = array(
	'var'   => array(),
	'array' => array()
);
$default = array();

$settings = array(
	'name'        => __( 'Post Formats', 'runway' ),
	'option_key'  => $shortname . 'post_formats',
	'fields'      => $fields,
	'default'     => $default,
	'parent_menu' => 'disabled', // 'framework-options',
	'file'        => __FILE__,
);

// Required components
include( 'object.php' );
global $post_formats, $post_formats_admin;
$post_formats = new Post_Formats_Object( $settings );

// Load admin components
if ( is_admin() ) {
	include( 'settings-object.php' );
	$post_formats_admin = new Post_Formats_Admin_Object( $settings );
}
