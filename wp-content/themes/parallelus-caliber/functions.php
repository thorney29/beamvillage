<?php if ( __FILE__ == $_SERVER['SCRIPT_FILENAME'] ) { die(); }


// Execute hooks before framework loads
do_action( 'functions_before' );


#-----------------------------------------------------------------
# Load framework
#-----------------------------------------------------------------
include_once get_template_directory() . '/framework/load.php';



// Execute hooks after framework loads
do_action( 'functions_after' ); ?><?php

/**
 * Theme registration and WP connections
 */

/**
 * Toggle template directory and URI for Runway child/standalone themes
 *
 * These functions can be used to replace the defaults in WordPress so the correct path is
 * generated for both child themes and standalone. It will ensure the paths being referenced
 * to your themes folder are always correct.
 */
if (!function_exists('rf_get_template_directory_uri')) :
	function rf_get_template_directory_uri() {
		return (IS_CHILD) ? get_stylesheet_directory_uri() : get_template_directory_uri();
	}
endif;
if (!function_exists('rf_get_template_directory')) :
	function rf_get_template_directory() {
		return (IS_CHILD) ? get_stylesheet_directory() : get_template_directory();
	}
endif;


/**
 * Set the content width based on the theme's design and stylesheet.
 */
if ( ! isset( $content_width ) )
	$content_width = 1130; /* pixels */


if ( ! function_exists( 'rf_theme_setup' ) ) :
/**
 * Set up theme defaults and register support for various WordPress features.
 *
 * Note that this function is hooked into the after_setup_theme hook, which runs
 * before the init hook. The init hook is too late for some features, such as indicating
 * support post thumbnails.
 */
function rf_theme_setup() {

	if ( function_exists( 'add_theme_support' ) ) {

		// WP Stuff
		add_editor_style(); // Admin editor styles
		add_theme_support( 'automatic-feed-links' ); // RSS feeds
		add_theme_support( 'title-tag' );
		add_theme_support( 'post-formats', array( 'gallery', 'audio', 'video', 'quote', 'link' ) ); // Post formats (unused: image, aside)
		register_nav_menu( 'primary', __( 'Primary Menu', 'parallelus-caliber' ) ); // Main menu

		// Post thumbnails
		add_theme_support( 'post-thumbnails' );
		set_post_thumbnail_size( 1200, 9999 );

		// Additional image sizes
		add_image_size( 'theme-blog', 1200, 800, true );           // Blog: post list image (4x3 ratio, hard crop)
		add_image_size( 'theme-blog-micro', 180, 120, true );      // Blog: micro image for fast load (4x3 ratio, hard crop)
		add_image_size( 'theme-portfolio', 800, 800, true );       // Portfolio: list image (1x1 ratio, hard crop)
		add_image_size( 'theme-portfolio-micro', 120, 120, true ); // Portfolio: micro image for fast load (1x1 ratio, hard crop)
		add_image_size( 'theme-header', 1920, 1080 );              // Header background (16:9 ratio)
		add_image_size( 'theme-header-micro', 192, 108 );        // Header background (16:9 ratio)
		add_image_size( 'theme-gallery', 1200, 900 );              // Gallery Post Format: single header
		add_image_size( 'theme-gallery-micro', 180, 135 );         // Gallery Post Format: micro version

		// WooCommerce
		add_theme_support( 'woocommerce' );
	}

	// Translation
	load_theme_textdomain( 'parallelus-caliber', rf_get_template_directory() . '/languages' );

	// Navigation menus
	register_nav_menus( array(
		'primary'  => __( 'Main Menu - right', 'parallelus-caliber' ),
		'menu-left'  => __( 'Alternate Menu - left', 'parallelus-caliber' ),
	) );

}
endif; // rf_theme_setup
add_action( 'after_setup_theme', 'rf_theme_setup' );


/**
 * Enqueue scripts and styles
 */
if ( ! function_exists( 'rf_theme_enqueue_scripts' ) ) :
function rf_theme_enqueue_scripts() {
	global $wp_scripts, $wp_styles;

	// Version number for query string
	$theme = wp_get_theme();
	$theme_version = ( $theme->exists() ) ? $theme->get( 'Version' ) : '1.0.0';
	$v = md5($theme_version);

	// Load CSS
	// check if 'font-awesome' already registered || enqueued
	if ( wp_style_is( 'font-awesome', 'registered' ) ) {
		wp_deregister_style( 'font-awesome' );
	}
	if ( wp_style_is( 'font-awesome', 'enqueued' ) ) {
		wp_dequeue_style( 'font-awesome' );
	}

	wp_enqueue_style( 'font-awesome', rf_get_template_directory_uri() . '/assets/css/font-awesome.min.css', '', '4.7.0' );
	wp_enqueue_style( 'theme-bootstrap', rf_get_template_directory_uri() . '/assets/css/bootstrap.min.css', '', $v ); // can be changed to 'bootstrap.css' for testing.
	wp_enqueue_style( 'theme-style', get_stylesheet_uri(), array( 'theme-bootstrap' ), $v );
	wp_enqueue_style( 'theme-ie-only', rf_get_template_directory_uri() . '/assets/css/ie-only.css', array( 'theme-style' ), $v );
	$wp_styles->add_data( 'theme-ie-only', 'conditional', 'lte IE 9' );

	// Load scripts
	wp_enqueue_script( 'theme-js', rf_get_template_directory_uri().'/assets/js/theme-scripts.js', array('jquery'), '1.0', true );
		wp_localize_script( 'theme-js', 'ThemeJS', array(
			'ajax_url'   => admin_url('admin-ajax.php'),
			'assets_url' => rf_get_template_directory_uri().'/assets/',
		) ); // localize for AJAX URL
	wp_enqueue_script( 'theme-bootstrapjs', rf_get_template_directory_uri().'/assets/js/bootstrap.min.js', array('jquery'), '1.0', true );
	wp_enqueue_script( 'fitvids', rf_get_template_directory_uri().'/assets/js/jquery.fitvids.min.js', array('jquery'), '1.1.0', true );
	wp_enqueue_script( 'flexibility-js', rf_get_template_directory_uri().'/assets/js/flexibility.js', array(), '1.0.6', true );
	wp_script_add_data( 'flexibility-js', 'conditional', 'lte IE 9' ); // Only include flex-box fix JS on IE9 or less.

	// IE only JS
	wp_enqueue_script( 'html5shiv', '//cdnjs.cloudflare.com/ajax/libs/html5shiv/3.7.2/html5shiv-printshiv.min.js', '3.7.2' ); // Source: https://cdnjs.com/libraries/html5shiv
	$wp_scripts->add_data( 'html5shiv', 'conditional', 'lt IE 9' );
	wp_enqueue_script( 'respondjs', '//cdnjs.cloudflare.com/ajax/libs/respond.js/1.4.2/respond.min.js', '1.4.2' ); // Source: https://cdnjs.com/libraries/respond.js
	$wp_scripts->add_data( 'respondjs', 'conditional', 'lt IE 9' );

	// IE10 viewport hack for Surface/desktop Windows 8 bug -->
	wp_enqueue_script( 'theme-ie10-viewport-bug', rf_get_template_directory_uri().'/assets/js/ie10-viewport-bug-workaround.js', '1.0.0', true );

	// Load comment reply ajax
	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}

	// Load keyboard navigation for image template
	if ( is_singular() && wp_attachment_is_image() ) {
		wp_enqueue_script( 'theme-keyboard-image-nav', rf_get_template_directory_uri() . '/assets/js/keyboard-image-nav.js', array( 'jquery' ), '1.0.0' );
	}

	// Google Fonts
	if ( get_options_data( 'options-page', 'font-body' ) == 'google' ) {
		$body_google_font = get_options_data( 'options-page', 'font-body-google' );
		$gFontQuery       = rf_google_font_query( $body_google_font, 'body' );
		// Load Google Font
		if ( ! empty( $gFontQuery ) ) {
			wp_enqueue_style( 'theme-google-font-body', $gFontQuery, array(), null );
		}
	}

	if ( get_options_data( 'options-page', 'font-heading' ) == 'google' ) {
		$heading_google_font = get_options_data( 'options-page', 'font-heading-google' );
		$gFontQuery          = rf_google_font_query( $heading_google_font, 'heading' );
		// Load Google Font
		if ( ! empty( $gFontQuery ) ) {
			wp_enqueue_style( 'theme-google-font-heading', $gFontQuery, array(), null );
		}
	}
}
endif;
add_action( 'wp_enqueue_scripts', 'rf_theme_enqueue_scripts' );


/**
 * Set the default header content template path.
 *
 * This is not the same as the WP header file. This file is used for
 * header design and content function within the theme.
 */
if ( ! function_exists( 'theme_cover_templates_filter' ) ) :
function theme_set_custom_header_template() {
	return 'templates/parts/header';
}
endif;
add_filter('rf_header_template', 'theme_set_custom_header_template' );

/**
 * Register the Cover style templates to be recognized by the theme
 *
 * Cover templates use a full screen background image and are only
 * recognized by the theme when properly registered.
 */
if ( ! function_exists( 'theme_cover_templates_filter' ) ) :
function theme_cover_templates_filter( $templates = array() ) {

	// Add templates to the array of cover templates
	$templates[] = 'templates/cover.php';
	$templates[] = 'templates/cover-with-menu.php';

	return $templates;
}
endif;
add_filter( 'rf_is_cover_template', 'theme_cover_templates_filter' ); 

/**
 * EXAMPLE CODE for Custom Skin CSS File
 *
 * You can load a custom Skin CSS file to the theme using the code
 * below. Uncomment or add this code to your child theme to include
 * a custom CSS file with your own CSS styles.
 */

/*
if ( ! function_exists( 'custom_theme_skin_css_file' ) ) :
function custom_theme_skin_css_file() {
	return get_stylesheet_directory_uri() . 'my-skin.css';
}
endif;
add_filter( 'theme_skin_css_file', 'custom_theme_skin_css_file' );
*/

/**
 * Prevent the creating of colorboxes by Simple colorbox plugin.
 * Сolorboxes should be created by the theme's scripts only
 */
if ( ! function_exists( 'theme_simple_colorbox_settings' ) ) :
function theme_simple_colorbox_settings( $colorbox_settings ) {
	$colorbox_settings['l10n_print_after'] = '';

	return $colorbox_settings;
}
endif;
add_filter( 'simple_colorbox_settings', 'theme_simple_colorbox_settings' );


/**
 * EXAMPLE CODE for custom BODY google font subsets
 */
//if ( ! function_exists( 'theme_body_font_subset' ) ) {
//	function theme_body_font_subset( array $subset ) {
//		$subset[] = 'cyrillic';
//		$subset[] = 'cyrillic-ext';
//
//		return $subset;
//	}
//}
//add_filter( 'google_font_subsets_body', 'theme_body_font_subset' );

/**
 * EXAMPLE CODE for custom HEADING google font subsets
 */
//if ( ! function_exists( 'theme_heading_font_subset' ) ) {
//	function theme_heading_font_subset( array $subset ) {
//		$subset[] = 'greek';
//
//		return $subset;
//	}
//}
//add_filter( 'google_font_subsets_heading', 'theme_heading_font_subset' );
