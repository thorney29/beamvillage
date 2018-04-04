<?php
/**
 * Add dynamic values to theme options by filter to include and
 * select from pages, categories, slide shows and other content
 * created by the user.
 */


#-----------------------------------------------------------------
# Include page list in 404 Error select for theme options
#-----------------------------------------------------------------

if (is_admin() && !function_exists('theme_option_404_content_select')) :
	function theme_option_404_content_select( $options ) {

		$current_language = theme_option_switch_lang('default');

		$allPages = get_pages();
		$options = array('default' => 'Default');

		if (is_array($allPages)) {
			foreach ($allPages as $page) {
				$options[$page->ID] = esc_attr($page->post_title);
			}
		}

		theme_option_switch_lang($current_language);

		return $options;
	}
	// add filter: [field alias]_data_options
	add_filter( 'error-page_data_options', 'theme_option_404_content_select' );
endif;


#-----------------------------------------------------------------
# POST Categories list select for theme options
#-----------------------------------------------------------------

if (is_admin() && !function_exists('theme_option_blog_categories_select')) :
	function theme_option_blog_categories_select( $options ) {

		$current_language = theme_option_switch_lang('default');

		$args = array(
			'hide_empty'    => 0,
			'hierarchical'  => 1,
			'taxonomy'      => 'category',
			// 'pad_counts' => false
		);
		$categories = get_categories( $args );
		if ( !empty( $categories ) && !is_wp_error( $categories ) ){

			$items = categories_order_by_hierarchy($categories);
			$options = (is_array($options) && !empty($options)) ? $options : array('none' => 'None');
			if (is_array($items)) {
				foreach ($items as $key => $value) {
					$level = count(get_ancestors($value->term_id, 'category'));
					$options[$value->term_id] = str_repeat('&mdash; &nbsp;', $level) . esc_html( $value->name .' ('.$value->count.')' );
				}
			}
		}

		theme_option_switch_lang($current_language);

		return $options;
	}
	// add filter: [field alias]_data_options
	add_filter( 'blog-post-categories_data_options', 'theme_option_blog_categories_select' );
	add_filter( 'next-prev-exclude-categories_data_options', 'theme_option_blog_categories_select' );
endif;


#-----------------------------------------------------------------
# POST Formats list select for theme options
#-----------------------------------------------------------------

if (is_admin() && !function_exists('theme_option_blog_formats_select')) :
	function theme_option_blog_formats_select( $options ) {

		$current_language = theme_option_switch_lang('default');

		$formats = get_terms( 'post_format');
		if ( !empty( $formats ) && !is_wp_error( $formats ) ){
			if (is_array($formats)) {
				foreach ($formats as $format) {
					if (is_object($format)){
						$options[$format->term_id] = esc_attr($format->name);
					}
				}
			}
		}

		theme_option_switch_lang($current_language);

		return $options;
	}
	// add filter: [field alias]_data_options
	add_filter( 'next-prev-exclude-post-formats_data_options', 'theme_option_blog_formats_select' );
endif;


#-----------------------------------------------------------------
# Include Content Blocks list select for theme options
#-----------------------------------------------------------------

if (is_admin() && !function_exists('theme_option_static_blocks_select')) :
	function theme_option_static_blocks_select( $options = array() ) {

		$current_language = theme_option_switch_lang('default');

		$args = array(
			'posts_per_page' => -1,
			'post_type' => 'static_block'
		);
		$items = get_posts($args);
		$options = (is_array($options) && !empty($options)) ? $options : array('disabled' => 'Disabled');
		if (is_array($items)) {
			foreach ($items as $key => $value) {
				$options[$value->ID] = esc_html( $value->post_title );
			}
		}

		theme_option_switch_lang($current_language);

		return $options;
	}
	// add filter: [field alias]_data_options
	add_filter( 'header-content-block_data_options', 'theme_option_static_blocks_select' );
	add_filter( 'footer-content-block_data_options', 'theme_option_static_blocks_select' );
	add_filter( 'theme_metabox_header_content_source', 'theme_option_static_blocks_select' );
endif;


#-----------------------------------------------------------------
# Include Revolution Slider list select for theme options
#-----------------------------------------------------------------

if (is_admin() && !function_exists('theme_option_rev_slider_select')) :
	function theme_option_rev_slider_select( $options ) {

		if (class_exists('RevSlider')) :

			$current_language = theme_option_switch_lang('default');

			$ss = new RevSlider();
			$arrSliders = $ss->getArrSliders();
			$options = array();
			if (count($arrSliders)) {
				foreach($arrSliders as $ss):
					// Slide data
					$id    = $ss->getID();
					$title = $ss->getTitle();
					$alias = $ss->getAlias();
					// Select options
					$options[$alias] = $title;
				endforeach;
			} else {
				$options = array('none' => __('No Sliders Created', 'runway'));
			}

			theme_option_switch_lang($current_language);
		else:
			$options = array('none' => __('Plugin not installed', 'runway'));
		endif; // class_exists('RevSlider')

		return $options;
	}
	// add filter: [field alias]_data_options
	add_filter( 'header-rev-slider-source_data_options', 'theme_option_rev_slider_select' );
endif;

// Creates one dimensional array ordered by category hierarchy
if (is_admin() && !function_exists('categories_order_by_hierarchy')) :
function categories_order_by_hierarchy( $items, $parent = 0 ) {
	$op = array();
	foreach( $items as $item ) {
		if( $item->category_parent == $parent ) {
			$op[$item->term_id] = $item;
			// using recursion
			$children = categories_order_by_hierarchy( $items, $item->term_id );
			if( $children ) {
				$op = array_merge($op, $children);
			}
		}
	}
	return $op;
}
endif;

if (is_admin() && !function_exists('theme_option_switch_lang')) :
	function theme_option_switch_lang( $lang = 'default' ) {
		global $sitepress;
		if(!isset($sitepress))  // if WPML is not active
			return;

		$default_language = $sitepress->get_default_language();

		if($lang == 'default') {
			$current_language = $sitepress->get_current_language();
			$sitepress->switch_lang($default_language);
			return $current_language;
		} else {
			$sitepress->switch_lang($lang);
		}
	}
endif;