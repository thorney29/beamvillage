<?php
/**
 * Filters to update and modify the output of content for template
 * files, theme functions and WordPress outputs.
 */


#-----------------------------------------------------------------
# Apply output filters on translation strings
#-----------------------------------------------------------------

if ( ! function_exists( 'rf_theme_escape_translations' ) ) :
function rf_theme_escape_translations( $translated_text, $text, $domain ) {

	// Escape output for theme text domain only
	if ( $domain == 'runway' || $domain == 'parallelus-caliber' ){
		return wp_kses_post($translated_text);
	}

    return $translated_text;

}
endif;
add_filter( 'gettext', 'rf_theme_escape_translations', 10, 3 );


#-----------------------------------------------------------------
# Body class on <body> tag.
#-----------------------------------------------------------------

if ( ! function_exists( 'theme_body_class' ) ) :
function theme_body_class( $classes ) {
	// check for "Boxed" body style
	$layout_style = get_options_data('options-page', 'theme-layout', 'full-width');
	if ($layout_style == 'boxed') {
		$classes[] = 'boxed';
	}

	// Transparent Navigation
	$menuOpacity = get_options_data('options-page', 'menu-opacity', '');
	if (isset($menuOpacity) && is_numeric($menuOpacity) && abs((int)$menuOpacity) < 100) {
		$classes[] = 'transparent-nav';
	}

	return $classes;
}
endif;
add_filter( 'body_class', 'theme_body_class' );


#-----------------------------------------------------------------
# Navbar class on main menu container.
#-----------------------------------------------------------------

if ( ! function_exists( 'theme_navbar_container_class' ) ) :
function theme_navbar_container_class( $classes ) {
	// check for "Boxed" body style
	$layout_style = get_options_data('options-page', 'theme-layout', 'full-width');
	if ($layout_style == 'boxed') {
		$classes[] = 'navbar-static-top';
	} else {
		$classes[] = 'navbar-fixed-top';
	}

	return $classes;
}
endif;
add_filter( 'theme_navbar_class', 'theme_navbar_container_class' );


#-----------------------------------------------------------------
# Header container class
#-----------------------------------------------------------------

if ( ! function_exists( 'theme_header_container_class' ) ) :
function theme_header_container_class( $class ) {

	$header_class = get_options_data('options-page', 'header-content-width', 'container-xl');
	if (!empty($header_class)) {
		return $header_class;
	}

	return $class;
}
endif;
add_filter( 'theme_header_container_class', 'theme_header_container_class' );


#-----------------------------------------------------------------
# Footer container class
#-----------------------------------------------------------------

if ( ! function_exists( 'theme_footer_container_class' ) ) :
function theme_footer_container_class( $class ) {

	$footer_class = get_options_data('options-page', 'footer-content-width', 'container-xl');
	if (!empty($footer_class)) {
		return $footer_class;
	}

	return $class;
}
endif;
add_filter( 'theme_footer_container_class', 'theme_footer_container_class' );


#-----------------------------------------------------------------
# Filter WordPress title to create page specific title tags.
#-----------------------------------------------------------------

if ( ! function_exists( 'rf_doc_title_parts' ) ) :
function rf_doc_title_parts( $parts ) {
	// global $paged;
	/*
	// Title of the viewed page.
	$parts['title'] = get_the_title();
	// Optional. Page number if paginated.
	$parts['page'] = $paged;
	// Optional. Site description when on home page.
	$parts['tagline'] = get_bloginfo( 'description', 'display' );
	// Optional. Site title when not on home page.
	$parts['site'] = get_bloginfo( 'name' );
	*/

	return $parts;
}
endif; // rf_doc_title_parts
add_filter( 'document_title_parts', 'rf_doc_title_parts', 10 );

if ( ! function_exists( 'rf_doc_title_sep' ) ) :
function rf_doc_title_sep( $sep ) {
	return '|';
}
endif; // rf_doc_title_parts
add_filter ( 'document_title_separator', 'rf_doc_title_sep', 10 );

#-----------------------------------------------------------------
# Filters for Header Styles
#-----------------------------------------------------------------

// Pages/Posts - Header Styles
// ................................................................
if ( ! function_exists( 'rf_page_header_bg' ) ) :
function rf_page_header_bg( $style = array() ) {

	// get the current page/post ID
	$id = get_queried_object_id();

	// No individual settings in these cases:
	//  - 1. Default blog posts on home
	//  - 2. Search results
	if ( (is_home() && is_front_page()) || is_search())
		return $style;

	// DEFAULTS
	// ----------------------------------------------------------------

	// Single posts (format: standard) - Header background should be the featured image.
	if ( is_single($id) && is_singular('post') && !get_post_format($id) ) {

		// Featured image background
		if (has_post_thumbnail( $id )) {
			$thumb_id = get_post_thumbnail_id( $id );
			$thumb_src = wp_get_attachment_image_src( $thumb_id, 'theme-header' );
			if ( isset($thumb_src[0]) && !empty($thumb_src[0]) ) {
				$style['background-image'] = 'url('. $thumb_src[0] .')';
			}
		}
	}

	// CUSTOM SETTINGS
	// ----------------------------------------------------------------

	// Override defaults based on meta settings (pages and posts)
	$meta_options  = get_post_custom( $id );
	$meta_header   = array();
	$meta_header['show']   = (isset($meta_options['theme_custom_layout_metabox_options_header_style'])) ? $meta_options['theme_custom_layout_metabox_options_header_style'][0] : '';
	$meta_header['bg']     = (isset($meta_options['theme_custom_header_metabox_options_header_bg'])) ? $meta_options['theme_custom_header_metabox_options_header_bg'][0] : 'default';
	$meta_header['custom'] = (isset($meta_options['theme_custom_header_metabox_options_custom_bg'])) ? $meta_options['theme_custom_header_metabox_options_custom_bg'][0] : 'default';

	if ( $meta_header['show'] == 'show' ) {

		// Header Background Image
		if ( $meta_header['bg'] !== 'default' ) {

			// No background image
			if ( $meta_header['bg'] == 'none' ) {

				$style['background-image'] = 'none';

			// Set a specific image
			} else {

				$thumb_src = array();

				// Featured image background
				if ( $meta_header['bg'] == 'featured-image' && has_post_thumbnail( $id )) {
					$thumb_id = get_post_thumbnail_id( $id );
					$thumb_src = wp_get_attachment_image_src( $thumb_id, 'theme-header' );
				}
				// Custom image background
				if ( $meta_header['bg'] == 'custom' && !empty($meta_header['custom']) ) {
					$thumb_src = wp_get_attachment_image_src( $meta_header['custom'], 'full' );
				}

				// Set the background image CSS property
				if ( isset($thumb_src[0]) && !empty($thumb_src[0]) ) {
					$style['background-image'] = 'url('. $thumb_src[0] .')';
				}
			}
		}
	}

	return $style;
}
endif;
add_filter( 'rf_get_header_style_attributes', 'rf_page_header_bg' );

// Pages/Posts - Hidden Header
// ................................................................
if ( ! function_exists( 'rf_page_show_hide_header' ) ) :
function rf_page_show_hide_header( $show_header ) {

	// Cover template must have header
	if (rf_is_cover_template())
		return true;

	// Blog page, but not the Home Page
	$blog_page = (is_home() && !is_front_page()) ? true : false; // WP page set to show blog posts (Settings > Reading)

	// Check the meta settings...
	if (is_page() || is_single() || $blog_page) {
		// Header on pages using meta options
		$meta_options = get_post_custom( get_queried_object_id() );
		if ( isset($meta_options['theme_custom_layout_metabox_options_header_style'][0]) ) {
			$style_setting = $meta_options['theme_custom_layout_metabox_options_header_style'][0];
			if ( isset($style_setting) && !empty($style_setting) && $style_setting != 'default' ) {
				$show_header = ($style_setting == 'none') ? false : true;
			}
		}
	}

	return $show_header;
}
endif;
add_filter( 'rf_has_custom_header', 'rf_page_show_hide_header' );


#-----------------------------------------------------------------
# Filters for Header Titles and Content
#-----------------------------------------------------------------

// Header title filters
// ................................................................
if ( ! function_exists( 'show_theme_header_title_filter' ) ) :
function show_theme_header_title_filter( $title = '' ) {

	// Pages and Posts
	if ( is_page() || is_single() ) {
		$title = ''; // no title in header, default for pages/posts
		// Meta options, title set to header
		if ( function_exists('rf_show_page_title') && rf_show_page_title('meta-value') === 'in-header' ) {
			$title = get_the_title( get_queried_object_id() ); // don't use get_the_ID(), it can change
		}
	}

	// Author
	if ( is_author() ) {
		// Get author info
		$author = get_queried_object();
		$posts_by = '<span class="author-name">'. __('Posts by', 'runway'). ' ' .$author->display_name .'</span>';
		$avatar = '<div class="author-avatar">'. get_avatar( get_the_author_meta( 'user_email' ), apply_filters( 'theme_author_bio_avatar_size', 120 ) ) .'</div>';
		// Create the title
		$title = $avatar . $posts_by;
	}

	return $title;
}
endif;
add_filter( 'theme_header_title', 'show_theme_header_title_filter', 11 );


// Header content filters
// ................................................................
if ( ! function_exists( 'show_theme_header_content_filter' ) ) :
function show_theme_header_content_filter( $content = '' ) {

	// No default content on search page.
	if (is_search())
		return $content;

	// Blog page, but not the Home Page
	$blog_page = (is_home() && !is_front_page()) ? true : false; // WP page set to show blog posts (Settings > Reading)

	// Pages and Posts
	if ( is_page() || is_single() || $blog_page ) {
		// get the current page/post ID
		$id = get_queried_object_id();

		// Override defaults based on meta settings (pages and posts)
		$meta_options  = get_post_custom( $id );
		$meta_header['show']    = (isset($meta_options['theme_custom_layout_metabox_options_header_style'])) ? $meta_options['theme_custom_layout_metabox_options_header_style'][0] : '';
		$meta_header['content'] = (isset($meta_options['theme_custom_header_metabox_options_header_source'])) ? $meta_options['theme_custom_header_metabox_options_header_source'][0] : '';

		// Meta options, page content
		if ( $meta_header['show'] == 'show' && $meta_header['content'] !== 'default' ) {
			if ($meta_header['content'] == 'none') {
				$content = '';
			} else {
				$content = the_static_block($meta_header['content'], array(), false);
			}
		}
	}

	// Archive
	if (is_archive()) {
		$term_description = term_description();
		if ( ! empty( $term_description ) ) :
			$content = sprintf( '<div class="taxonomy-description">%s</div>', $term_description );
		endif;
	}

	// Author
	if ( is_author() ) {
		// Intro Text / Sub-title
		if ( get_the_author_meta( 'description' ) ) {
			$content = get_the_author_meta( 'description' );
		}
	}

	return $content;
}
endif;
add_filter( 'theme_header_content', 'show_theme_header_content_filter', 11 );

// Author Avatar in Header
if ( ! function_exists( 'show_theme_header_author_avatar' ) ) :
function show_theme_header_author_avatar( $content = '' ) {

	// Check the theme options to make sure this is enabled
	if (get_options_data('options-page', 'post-single-author-header', 'show') !== 'show') {
		return $content;
	}

	// Posts (standard format)
	if ( is_single() && is_singular('post') && !get_post_format() ) {
		$author_id = get_post_field( 'post_author', get_queried_object_id() );
		$content .= get_theme_author_avatar();
	}

	return $content;
}
endif;
add_filter( 'theme_header_content', 'show_theme_header_author_avatar', 12 );

// Get the Author Avatar in container
if ( ! function_exists( 'get_theme_author_avatar' ) ) :
function get_theme_author_avatar() {

	$content = '';
	$author_id = get_post_field( 'post_author', get_queried_object_id() );

	// Put together the author avatar graphic (for headers)
	$content .= '<div class="author-avatar">';
	$content .= '<a href="'. get_author_posts_url( $author_id ) .'" title="'. __('Posts by', 'runway') .' '. get_the_author_meta('display_name', $author_id) .'">';
	$content .= get_avatar( $author_id, 100, '', get_the_author_meta('display_name', $author_id) );
	$content .= '</a></div>';

	return $content;
}
endif;


#-----------------------------------------------------------------
# Custom 404 Page from Theme Options
#-----------------------------------------------------------------

// Redirect to the 404 page and template
//-----------------------------------------------------------------
/**
 * Based on: https://wordpress.org/plugins/404page/
 */
if ( ! function_exists( 'theme_custom_error_page' ) ) :
function theme_custom_error_page( $template ) {
	global $wp_query;

	$page_id = get_options_data('options-page', 'error-page', '');

	if ( !empty($page_id) && $page_id > 0 ) {
		// start the query over
		$wp_query = null;
		$wp_query = new WP_Query();
		$wp_query->query( 'page_id=' . $page_id );
		$wp_query->the_post();

		// Get the template
		$template = get_page_template();

		rewind_posts(); // clean up
	}

	return $template;
}
endif;
add_filter( '404_template', 'theme_custom_error_page' );


#-----------------------------------------------------------------
# Filters for plugin: Beaver Builder
#-----------------------------------------------------------------
// BB Upgrade
//................................................................
if ( ! function_exists( 'parallelus_fl_builder_upgrade_url' ) ) :
	function parallelus_fl_builder_upgrade_url( $url ) {

		return 'http://para.llel.us/+/beaverbuilder';
	}
	add_filter( 'fl_builder_upgrade_url', 'parallelus_fl_builder_upgrade_url', 9999 );
endif;


#-----------------------------------------------------------------
# Filters for plugin: Ninja Forms
#-----------------------------------------------------------------
// NF Upgrade
//................................................................
if ( ! function_exists( 'parallelus_ninja_forms_affiliate_id' ) ) :
	function parallelus_ninja_forms_affiliate_id( $id ) {

		return '1311242';
	}
	add_filter( 'ninja_forms_affiliate_id', 'parallelus_ninja_forms_affiliate_id', 9999 );
endif;
