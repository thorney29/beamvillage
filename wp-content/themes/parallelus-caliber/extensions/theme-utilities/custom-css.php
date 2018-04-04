<?php

// Make a new object for the CSS cache
if ( class_exists('ThemeCacheFiles') ) {
	$theme_cache_css = new ThemeCacheFiles();
}

#-----------------------------------------------------------------
# Minify and cache custom CSS
#-----------------------------------------------------------------

// Minify CSS
//-----------------------------------------------------------------
/**
 * Based on: http://davidwalsh.name/css-compression-php
 */

if ( ! function_exists( 'theme_minify_css' ) ) :
function theme_minify_css( $css = '' ) {

	if ( ! defined( 'WP_DEBUG' ) || ! WP_DEBUG ) {
		$css = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $css); // Remove comments
		$css = str_replace(array( ': ', '; ', '} ', ' }', '{ ', ' {', ', ' ), array( ':', ';', '}', '}', '{', '{', ',' ), $css); // Remove spaces before and after symbols
		$css = str_replace(array("\r\n","\r","\n","\t",'  ','    ','    '), '', $css); // Remove remaining whitespace
	}

	return $css;
}
endif;

// Cache the custom CSS (saves it to DB, not true "caching")
//-----------------------------------------------------------------
/**
 * The CSS cache filename
 */
if ( ! function_exists( 'theme_cache_css_filename' ) ) :
function theme_cache_css_filename() {
	global $shortname;

	$fileName = md5($shortname.get_current_blog_id()).'.css';

	return $fileName;
}
endif;

/**
 * Checks for a cache file
 */
if ( ! function_exists( 'theme_has_cache_css_file' ) ) :
function theme_has_cache_css_file() {
	global $theme_cache_css;

	$fileName = theme_cache_css_filename();
	return ($theme_cache_css->cache_file_exists( $fileName )) ? $fileName : false;

}
endif;

/**
 * Get's the cache file URL
 */
if ( ! function_exists( 'theme_get_cache_css_url' ) ) :
function theme_get_cache_css_url() {
	global $theme_cache_css;

	$fileName = theme_cache_css_filename();
	$cacheDir = $theme_cache_css->get_cache_dir();
	$filePath = $cacheDir['url'] . $fileName;

	return $filePath;
}
endif;

/**
 * Get's the cache file URL
 */
if ( ! function_exists( 'theme_get_cache_version' ) ) :
function theme_get_cache_version() {
	global $theme_cache_css;

	$fileName = theme_cache_css_filename();
	$version = $theme_cache_css->get_asset_version( $fileName );

	return $version;
}
endif;

/**
 * Calling with no params will store (cache) the minified CSS
 *
 * Possible $return param valuse:
 * 	'alias' = return the alias of the cache DB field
 * 	'css' = return the minified css
 *  'cache' = cached database data
 *  'update' = update the cache in the DB
 */
if ( ! function_exists( 'theme_cache_custom_css' ) ) :
function theme_cache_custom_css( $return = false ) {
	global $shortname, $theme_cache_css;

	$cacheAlias = md5($shortname).'_cacheCSS';

	if ($return == 'alias') {
		return $cacheAlias;
	}
	if ($return == 'cache') {
		return get_option( $cacheAlias );
	}
	// Clear old cache value
	if ($return == 'reset') {
		return update_option( $cacheAlias, '' );
	}

	// Get the compiled CSS from theme options
	$customCSS = theme_custom_styles();

	// Prepare the CSS
	if (!empty($customCSS)) {

		// Minify
		$css = theme_minify_css($customCSS);
		if ($return == 'css') {
			return $css;
		}

		// Update the cache in DB
		if ($return == 'update') {

			// Update DB field
			update_option( $cacheAlias, $css );

			// save cache CSS file
			$fileName = theme_cache_css_filename();
			$theme_cache_css->save_cache_file( $fileName, $css );

			return $css;
		}
	}
}
endif;

// Clear the CSS cache in DB
//-----------------------------------------------------------------
if ( ! function_exists( 'theme_cache_update_custom_css' ) ) :
function theme_cache_update_custom_css($option, $old_value, $value) {
	global $shortname;

	/**
	 * When the theme options are updated we delete the saved cache
	 * value. Next time the site loads it will notice the empty
	 * cache and create it again automatically
	 *
	 * NOTE: The $shortname.{alias} value must match the alias set
	 * for the options builder page in the theme.
	 */
	if ( $option == $shortname."options-page") {
		theme_cache_custom_css( 'reset' );
	}
}
endif;
add_action( "update_option", 'theme_cache_update_custom_css', 10, 3 );



#-----------------------------------------------------------------
# Include Custom CSS in Theme Header
#-----------------------------------------------------------------

// Get custom styles cache (or backup theme options)
//-----------------------------------------------------------------
if ( ! function_exists( 'theme_get_cache_styles' ) ) :
function theme_get_cache_styles() {
	global $wp_customize;

	$customCSS = '';

	// Custom Styles
	$customCSS = theme_cache_custom_css('cache'); // get the cached CSS

	if (empty($customCSS) || !empty($wp_customize)) {
		// maybe there's no cache because of some error, or it's been cleared and we need to recreate it.
		// or maybe this is just the Customizer view and we want live updates...
		$customCSS = theme_cache_custom_css('update');
	}

	return  $customCSS; // escaped above
}
endif;


// Add styles to header or include cache file
//-----------------------------------------------------------------
if ( ! function_exists( 'theme_options_custom_css' ) ) :
function theme_options_custom_css() {

	$custom_css = theme_get_cache_styles();

	if (!empty($custom_css)) {

		// check for skin CSS files
		$skin_file = apply_filters( 'theme_skin_css_file', ''); // child themes can use this to specify the URL of a skin CSS file
		$handle = (!empty($skin_file) && $skin_file !== 'none') ? 'theme-skin' : 'theme-style';

		// Check for cache CSS file
		if ( theme_has_cache_css_file() ) {
			// enquque the cache file
			wp_enqueue_style( 'theme-custom', theme_get_cache_css_url(), array( $handle ), theme_get_cache_version() );
		} else {
			// include the inline styles
			wp_add_inline_style( $handle, $custom_css ); // $handle must match existing CSS file.
		}
	}
}
endif;
add_action( 'wp_enqueue_scripts', 'theme_options_custom_css', 11 );


// Get custom styles from theme options
//-----------------------------------------------------------------
if ( ! function_exists( 'theme_custom_styles' ) ) :
function theme_custom_styles() {

	// Styles variable
	$CustomStyles = '';

	#-----------------------------------------------------------------
	# Styles from Theme Options
	#-----------------------------------------------------------------

	// Accent Color - Primary
	//................................................................

	$accent_index = array('1','2','3');

	// Accent Colors
	foreach( $accent_index as $index ) {
		$accent_color[$index] = get_options_data('options-page', 'color-accent-'.$index);

		if (!empty($accent_color[$index]) && $accent_color[$index] !== '#') {

			// get the color so we can modify it.
			$color = new Color($accent_color[$index]);
			// text over accent color
			$color_alt = $color->lighten(10);
			$color_text = rf_get_as_rgba('#ffffff', 0.9);
			$color_bg_alt = $color->lighten(10);
			$color_text_alt = $color->lighten(20);
			if ($color->isLight()) {
				$color_alt = $color->darken(10);
				$color_text = rf_get_as_rgba('#000000', 0.9);
				$color_bg_alt = $color->darken(10);
				$color_text_alt = $color->darken(20);
			}

			$accentStyles  = '';

			// Color 1 Only
			//................................................................
			if ($index == '1') {
				// Accent Background
				$accentStyles .= '.accent-1-bg, .bg-primary, .btn-primary, input[type="button"].btn-primary, input[type="submit"], .nf-fields .field-wrap input[type="button"].ninja-forms-field, .nf-fields .field-wrap input[type="submit"].ninja-forms-field, .portfolio-grid-item:before, .btn-primary[disabled], .btn-primary[disabled]:hover, .btn-primary.disabled:focus, .btn-primary[disabled]:focus, .btn-primary[disabled]:active, input[type="submit"][disabled], input[type="submit"][disabled]:hover, input[type="submit"][disabled]:focus, input[type="submit"][disabled]:active, .dropdown-menu > .active > a, .dropdown-menu > .active > a:hover, .dropdown-menu > .active > a:focus, .nav-pills > li.active > a, .nav-pills > li.active > a:hover, .nav-pills > li.active > a:focus, .label-primary, .progress-bar, .panel-primary > .panel-heading, #loginform p.submit input[type="submit"], #login #lostpasswordform p.submit input[type="submit"], .btn-group .dropdown-toggle.btn-primary ~ .dropdown-menu, .input-group-btn .dropdown-toggle.btn-primary ~ .dropdown-menu, .btn-group .dropdown-toggleinput[type="submit"] ~ .dropdown-menu, .input-group-btn .dropdown-toggleinput[type="submit"] ~ .dropdown-menu, .edd-submit, a.edd-submit:link, a.edd-submit:visited, .edd-submit:active, a.edd-submit:active, .edd-submit.active { background-color: #'. $color->getHex() .'; color: '.$color_text.'; }';
				// Alternate Background (hover: lighten/darken)
				$accentStyles .= 'a.bg-primary:hover, a.bg-primary:focus, .btn-primary:focus, input[type="button"].btn-primary:focus, input[type="submit"]:focus, .nf-fields .field-wrap input[type="button"].ninja-forms-field:focus, .nf-fields .field-wrap input[type="submit"].ninja-forms-field:focus, .btn-primary:hover, input[type="button"].btn-primary:hover, input[type="submit"]:hover, .nf-fields .field-wrap input[type="button"].ninja-forms-field:hover, .nf-fields .field-wrap input[type="submit"].ninja-forms-field:hover, .btn-primary:active, input[type="button"].btn-primary:active, .open > .dropdown-toggle.btn-primary, input[type="submit"]:active, .open > .dropdown-toggleinput[type="submit"], .btn-primary:active:hover, .btn-primary.active:hover, .open > .dropdown-toggle.btn-primary:hover, .btn-primary:active:focus, input[type="button"].btn-primary:active:focus, .open > .dropdown-toggle.btn-primary:focus, input[type="submit"]:active:hover, .open > .dropdown-toggleinput[type="submit"]:hover, input[type="submit"]:active:focus, .open > .dropdown-toggleinput[type="submit"]:focus, .label-primary[href]:hover, .label-primary[href]:focus, #loginform p.submit input[type="submit"]:focus, #login #lostpasswordform p.submit input[type="submit"]:focus, #loginform p.submit input[type="submit"].focus, #login #lostpasswordform p.submit input[type="submit"].focus, #loginform p.submit input[type="submit"]:hover, #login #lostpasswordform p.submit input[type="submit"]:hover, .btn-group .dropdown-toggle.btn-primary ~ .dropdown-menu > li > a:hover, .input-group-btn .dropdown-toggle.btn-primary ~ .dropdown-menu > li > a:hover, .btn-group .dropdown-toggleinput[type="submit"] ~ .dropdown-menu > li > a:hover, .input-group-btn .dropdown-toggleinput[type="submit"] ~ .dropdown-menu > li > a:hover, .edd-submit:hover, .edd-submit:focus, .edd-submit.focus, a.edd-submit:hover, a.edd-submit:focus, a.edd-submit.focus, .edd-submit:active:hover { background-color: #'. $color_bg_alt .'; color: '.$color_text.'; }';
				// Accent Text
				$accentStyles .= '.accent-1-text, a, .text-primary, .pre-heading, .btn-primary .badge, input[type="submit"] .badge, .btn-link, .nav-pills > .active > a > .badge, .panel-primary > .panel-heading .badge, #loginform p.submit input[type="submit"] .badge, #login #lostpasswordform p.submit input[type="submit"] .badge { color: #'. $color->getHex() .'; }';
				// Alternate Text (hover: lighten/darken)
				$accentStyles .= 'a.accent-1-text:hover, a:hover, a:focus, a.text-primary:hover, a.text-primary:focus, .btn-link:hover, .btn-link:focus, .post-nav-bottom .post-navigation .pager .nav-previous a:hover, .post-nav-bottom .post-navigation .pager .nav-next a:hover, .page-header h1 a:hover, .entry-header h1 a:hover, .page-header h2 a:hover, .entry-header h2 a:hover, .page-header h3 a:hover, .entry-header h3 a:hover, .page-header .entry-title a:hover, .entry-header .entry-title a:hover, .widget_recent_author_entries ul li a:hover, .widget_recent_entries ul li a:hover { color: #'. $color_text_alt.'; }';
				// Border color
				$accentStyles .= '.accent-1-border, .btn-primary, input[type="button"].btn-primary, input[type="submit"], .nav .open > a, .nav .open > a:hover, .nav .open > a:focus, a.thumbnail:hover, a.thumbnail:focus, a.thumbnail.active, .panel-primary, .panel-primary > .panel-heading, #loginform p.submit input[type="submit"], #login #lostpasswordform p.submit input[type="submit"], .edd-submit, .nf-fields .field-wrap input[type="button"].ninja-forms-field, .nf-fields .field-wrap input[type="submit"].ninja-forms-field { border-color: #'. $color->getHex() .'; }';
				$accentStyles .= '.panel-primary > .panel-heading + .panel-collapse > .panel-body, .top-nav.navbar .navbar-nav > li.active, .btn-group .dropdown-toggle.btn-primary ~ .dropdown-menu, .input-group-btn .dropdown-toggle.btn-primary ~ .dropdown-menu, .btn-group .dropdown-toggleinput[type="submit"] ~ .dropdown-menu, .input-group-btn .dropdown-toggleinput[type="submit"] ~ .dropdown-menu { border-top-color: #'. $color->getHex() .'; }';
				$accentStyles .= '.panel-primary > .panel-footer + .panel-collapse > .panel-body { border-bottom-color: #'. $color->getHex() .'; }';
				// Alternate Border color
				$accentStyles .= '.btn-primary:focus, .btn-primary.focus, input[type="button"].btn-primary:focus, input[type="submit"]:focus, .btn-primary:hover, input[type="button"].btn-primary:hover, input[type="submit"]:hover, .btn-primary:active, input[type="button"].btn-primary:active, .open > .dropdown-toggle.btn-primary, input[type="submit"]:active, input[type=" submit"].active, .open > .dropdown-toggleinput[type="submit"], .btn-primary:active:hover, .btn-primary.active:hover, .open > .dropdown-toggle.btn-primary:hover, .btn-primary:active:focus, input[type="button"].btn-primary:active:focus, .open > .dropdown-toggle.btn-primary:focus, .btn-primary:active.focus, .btn-primary.active.focus, .open > .dropdown-toggle.btn-primary.focus, input[type="submit"]:active:hover, input[type="submit"].active:hover, .open > .dropdown-toggleinput[type="submit"]:hover, input[type="submit"]:active:focus, input[type="submit"].active:focus, .open > .dropdown-toggleinput[type="submit"]:focus, input[type="submit"]:active.focus, input[type="submit"].active.focus, .open > .dropdown-toggleinput[type="submit"].focus, .edd-submit:hover, .edd-submit:focus, .nf-fields .field-wrap input[type="button"].ninja-forms-field:focus, .nf-fields .field-wrap input[type="submit"].ninja-forms-field:focus, .nf-fields .field-wrap input[type="button"].ninja-forms-field:hover, .nf-fields .field-wrap input[type="submit"].ninja-forms-field:hover { border-color: #'. $color_bg_alt .'; }';
			}

			// Color 2 Only
			//................................................................
			if ($index == '2') {
				// Accent Background
				$accentStyles .= '.accent-2-bg { background-color: #'. $color->getHex() .'; color: '.$color_text.'; }';
				// Accent Text
				$accentStyles .= '.accent-2-text, h1 em, h2 em, h3 em, h4 em, h5 em, h6 em, .h1 em, .h2 em, .h3 em, .h4 em, .h5 em, .h6 em { color: #'. $color->getHex() .'; }';
				// Alternate Text (hover: lighten/darken)
				$accentStyles .= 'a.accent-2-text:hover { color: #'. $color_text_alt.'; }';
			}

			// Add styles to CSS variable
			$CustomStyles .= $accentStyles;
		}

		unset($color);
	}


	// Body Background
	//................................................................

	$bodyStyles = '';

	// Color
	$bodyColor = (get_options_data('options-page', 'body-bg') == 'color') ? get_options_data('options-page', 'body-bg-color') : '';
	if (!empty($bodyColor) && $bodyColor != '#') {
		$bodyStyles .= " background-color: ". $bodyColor .";";
	}
	// Image
	$bodyImage = (get_options_data('options-page', 'body-bg') == 'image') ? get_options_data('options-page', 'body-bg-image') : '';
	if (!empty($bodyImage)) {
		$bodyStyles .= " background-image: url(". $bodyImage .");";
		$bodyStyles .= " background-repeat: ". get_options_data('options-page', 'body-bg-repeat', 'no-repeat') .";";
		$bodyStyles .= " background-attachment:  ". get_options_data('options-page', 'body-bg-attachment', 'scroll') .";";
		$bodyStyles .= " background-position:  ". get_options_data('options-page', 'body-bg-position', 'center') .";";
		$bodyBgSize = get_options_data('options-page', 'body-bg-size', 'auto');
		if ($bodyBgSize !== 'none') {
			$bodyStyles .= " background-size:  ". $bodyBgSize .";";
		}
	}
	// Add styles to CSS variable
	if (!empty($bodyStyles)) {
		$CustomStyles .= 'body, body.boxed {'. $bodyStyles .' }';
		// Replace the boxed border (made to look like a shadow) with a very subtle shadow
		$CustomStyles .= 'body.boxed #wrapper { border: 0;	box-shadow: 0 0 5px rgba(0,0,0,.2); }';
	}


	// Links
	//................................................................

	$linkColor = get_options_data('options-page', 'link-color');
	if (!empty($linkColor) && $linkColor != '#') {
		$linkStyles  = "a, .widget a, .btn.btn-link { color: ". $linkColor ."; }";
		$linkStyles .= ".btn.btn-link { border-color: ". $linkColor ."; }";
		// Add styles to CSS variable
		$CustomStyles .= $linkStyles;
	}
	// Hover (links)
	$hoverColor = get_options_data('options-page', 'link-hover-color');
	if (!empty($hoverColor) && $hoverColor != '#') {
		$linkHoverStyles  = "a:hover, a:focus, .widget a:hover, .widget_recent_author_entries ul li a:hover, .widget_recent_entries ul li a:hover, .btn.btn-link:hover, .btn.btn-link:focus, .page-header h1 a:hover, .entry-header h1 a:hover, .page-header h2 a:hover, .entry-header h2 a:hover, .page-header h3 a:hover, .entry-header h3 a:hover, .page-header .entry-title a:hover, .entry-header .entry-title a:hover, .search-result h3 a:hover, .search-result .result-title a:hover { color: ". $hoverColor ."; }";
		$linkHoverStyles .= ".btn.btn-link:hover, .btn.btn-link:focus { border-color: ". $hoverColor ."; }";
		// Add styles to CSS variable
		$CustomStyles .= $linkHoverStyles;
	}


	// Grays
	//................................................................

	$grayLightest = get_options_data('options-page', 'color-gray-lighter');
	$grayLight    = get_options_data('options-page', 'color-gray-light');
	$grayMedium   = get_options_data('options-page', 'color-gray');
	$grayDark     = get_options_data('options-page', 'color-gray-dark');
	$grayDarkest  = get_options_data('options-page', 'color-gray-darker');
	$grayStyles   = '';

	// Lightest
	if (!empty($grayLightest) && $grayLightest != '#') {
		// color  .paging-nav-bottom .paging .pagination > li > a, .paging-nav-bottom.section-wrapper .paging .pagination>li>a
		$grayStyles .= ".btn-link[disabled]:hover, fieldset[disabled] .btn-link:hover, .btn-link[disabled]:focus, fieldset[disabled] .btn-link:focus, .badge, #footer h1, #footer h2, #footer h3, #footer h4, #footer h5, #footer h6, #footer .h1, #footer .h2, #footer .h3, #footer .h4, #footer .h5, #footer .h6, #footer a:hover, #footer a:active, #wrapper .paging-nav-bottom .paging .pagination > li > a, #wrapper .paging-nav-bottom .paging .pagination > li > span, #wrapper .paging-nav-bottom .paging .pagination > li a:hover { color: ". $grayLightest ."; }";
		// border-color
		$grayStyles .= ".form-control, input, textarea, select, .nf-fields .field-wrap .ninja-forms-field, .pagination > li > a, .pagination > li > span, .pagination > .disabled > span, .pagination > .disabled > span:hover, .pagination > .disabled > span:focus, .pagination > .disabled > a, .pagination > .disabled > a:hover, .pagination > .disabled > a:focus, blockquote { border-color: ". $grayLightest ."; }";
		// background-color
		$grayStyles .= "pre, .table-striped > tbody > tr:nth-of-type(odd), .table-hover > tbody > tr:hover, .table > thead > tr > td.active, .table > tbody > tr > td.active, .table > tfoot > tr > td.active, .table > thead > tr > th.active, .table > tbody > tr > th.active, .table > tfoot > tr > th.active, .table > thead > tr.active > td, .table > tbody > tr.active > td, .table > tfoot > tr.active > td, .table > thead > tr.active > th, .table > tbody > tr.active > th, .table > tfoot > tr.active > th, .form-control, .form-control, input, textarea, select, .nf-fields .field-wrap .ninja-forms-field, .nav > li > a:hover, .nav > li > a:focus, .nav .open > a, .nav .open > a:hover, .nav .open > a:focus, .breadcrumb, .pagination > li > a, .pagination > li > span, .pager li > a, .pager li > span, .pager .disabled > a, .pager .disabled > a:hover, .pager .disabled > a:focus, .pager .disabled > span, .jumbotron, .well, .container-post, .container-post .author-avatar, .container-post .post-format-icon, .masthead .author-avatar, .masthead .post-format-icon, .post-footer, .navbar-default .dropdown-menu > li > a:hover, .navbar-default .dropdown-menu > .active > a, .navbar-default .dropdown-menu > .active > a:hover, .nav-justified:not([class*='nav-pills']):not([class*='nav-tabs']) { background-color: ". $grayLightest ."; }";
	}
	// Light
	if (!empty($grayLight) && $grayLight != '#') {
		// color
		$grayStyles .= "h1 small, h2 small, h3 small, h4 small, h5 small, h6 small, .h1 small, .h2 small, .h3 small, .h4 small, .h5 small, .h6 small, h1 .small, h2 .small, h3 .small, h4 .small, h5 .small, h6 .small, .h1 .small, .h2 .small, .h3 .small, .h4 .small, .h5 .small, .h6 .small, .text-muted, caption, .nav > li.disabled > a, .nav > li.disabled > a:hover, .nav > li.disabled > a:focus, .breadcrumb > li + li:before, .pagination > .disabled > span, .pagination > .disabled > span:hover, .pagination > .disabled > span:focus, .pagination > .disabled > a, .pagination > .disabled > a:hover, .pagination > .disabled > a:focus, .pager .disabled > a, .pager .disabled > a:hover, .pager .disabled > a:focus, .pager .disabled > span, #footer a, .entry-meta .sep, .entry-meta a, blockquote, .help-block, .ninja-forms-field-description, .ninja-forms-required-items, .ninja-forms-field-error, .ninja-forms-response-msg { color: ". $grayLight ."; }";
		// border-color
		$grayStyles .= ".form-control:hover, input:hover, textarea:hover, select:hover, .nf-fields .field-wrap .ninja-forms-field:hover, .form-control:focus, input:focus, textarea:focus, select:focus, .nf-fields .field-wrap .ninja-forms-field:focus { border-color: ". $grayLight ."; }";
		$grayStyles .= "abbr[title], abbr[data-original-title] { border-bottom-color: ". $grayLight ."; }";
		// background-color
		$grayStyles .= ".label-default, .section-wrapper.post-audio, .container-post .section-post-thumbnail .entry-thumbnail-cover, .container-post .author-avatar .post-icon, .container-post .post-format-icon .post-icon, .masthead .author-avatar .post-icon, .masthead .post-format-icon .post-icon { background-color: ". $grayLight ."; }";
		// Button and hover color
		$btnColor = new Color($grayLight);
		$btnText = ($btnColor->isDark()) ? rf_get_as_rgba('#ffffff', 0.9) : rf_get_as_rgba('#000000', 0.9);
		$btnHover = ($btnColor->isDark()) ? '#'.$btnColor->lighten(12) : '#'.$btnColor->darken(12);
		$btnHoverColor = new Color($btnHover);
		$btnHoverText = ($btnHoverColor->isDark()) ? rf_get_as_rgba('#ffffff', 0.9) : rf_get_as_rgba('#000000', 0.9);
		unset($btnColor);
		unset($btnHoverColor);
		$grayStyles .= ".btn-default, .btn-default[disabled], .btn-default[disabled]:hover, .btn-default[disabled]:focus, .btn-default[disabled]:active, input[type='button'], input[type='submit'] { background-color: ". $grayLight ."; border-color: ". $grayLight ."; color: ". $btnText ."; }";
		$grayStyles .= ".btn:hover, .btn:focus, .btn.focus, button:hover, button:focus, button.focus, input[type='button']:hover, input[type='button']:focus, input[type='button'].focus, input[type='submit']:hover, input[type='submit']:focus, input[type='submit'].focus, .btn-default:focus, .btn-default.focus, .btn-default:hover, .btn-default:active, .btn-default.active, .open > .dropdown-toggle.btn-default, .btn-default:active:hover, .btn-default.active:hover, .open > .dropdown-toggle.btn-default:hover, .btn-default:active:focus, .btn-default.active:focus, .open > .dropdown-toggle.btn-default:focus, .btn-default:active.focus, .btn-default.active.focus, .open > .dropdown-toggle.btn-default.focus, .btn-group .dropdown-toggle.btn-default ~ .dropdown-menu > li > a, .input-group-btn .dropdown-toggle.btn-default ~ .dropdown-menu > li > a { background-color: ". $btnHover ."; border-color: ". $btnHover ."; color: ". $btnHoverText ."; }";	}
	// Medium
	if (!empty($grayMedium) && $grayMedium != '#') {
		// border-color
		$grayStyles .= ".nav-tabs .caret { border-top-color: ". $grayMedium ."; border-bottom-color: ". $grayMedium ."; }";
		// background-color
		$grayStyles .= ".section-wrapper.post-gallery, .cover #header, .cover-wrapper { background-color: ". $grayMedium ."; }";
	}
	// Dark
	if (!empty($grayDark) && $grayDark != '#') {
		// color
		$grayStyles .= "legend, .pagination > li > span, .panel-default > .panel-heading, .comment-list .media-body .comment-meta a:hover, .label-default { color: ". $grayDark ."; }";
		// border-color
		$grayStyles .= ".popover { border-color: ". $grayDark ."; }";
		$grayStyles .= ".tooltip.top .tooltip-arrow, .tooltip.top-left .tooltip-arrow, .tooltip.top-right .tooltip-arrow, .popover.top > .arrow, .popover.top > .arrow:after { border-top-color: ". $grayDark ."; }";
		$grayStyles .= ".tooltip.right .tooltip-arrow, .popover.right > .arrow, .popover.right > .arrow:after { border-right-color: ". $grayDark ."; }";
		$grayStyles .= ".tooltip.left .tooltip-arrow, .popover.left > .arrow, .popover.left > .arrow:after { border-left-color: ". $grayDark ."; }";
		$grayStyles .= ".tooltip.bottom .tooltip-arrow, .tooltip.bottom-left .tooltip-arrow, .tooltip.bottom-right .tooltip-arrow, .popover.bottom > .arrow, .popover.bottom > .arrow:after { border-bottom-color: ". $grayDark ."; }";
		// background-color
		$grayStyles .= ".btn-default .badge, .panel-default > .panel-heading .badge, .modal-backdrop, .tooltip-inner, .popover, .popover-title, #wrapper .paging-nav-bottom, .post-nav-bottom { background-color: ". $grayDark ."; }";
	}
	// Darkest
	if (!empty($grayDarkest) && $grayDarkest != '#') {
		// color
		$grayStyles .= ".pagination > li > a:hover, .pagination > li > span:hover, .pagination > li > a:focus, .pagination > li > span:focus, .pagination > .active > a, .pagination > .active > span, .pagination > .active > a:hover, .pagination > .active > span:hover, .pagination > .active > a:focus, .pagination > .active > span:focus { color: ". $grayDarkest ."; }";
		// background-color
		$grayStyles .= "#footer { background-color: ". $grayDarkest ."; }";
	}

	if (!empty($grayStyles)) {
		// Add styles to CSS variable
		$CustomStyles .= $grayStyles;
	}


	// Navigation Menus
	//----------------------------------------------------------------
	$menuStyles = '';
	$menuBackground = get_options_data('options-page', 'menu-background');
	$menuOpacity = get_options_data('options-page', 'menu-opacity', '');
	$menuTextColor = get_options_data('options-page', 'menu-text-color');
	$menuAccentColor = get_options_data('options-page', 'menu-accent');
	$menuSubNavColor = get_options_data('options-page', 'menu-drop-down');
	$menuSubNavText = get_options_data('options-page', 'menu-drop-down-text');
	$menuSubNavHover = rf_get_as_rgba('#000000', 0.15);
	// Text color
	$menuTextColor = (!empty($menuTextColor) && $menuTextColor !== '#') ? $menuTextColor : '';
	$menuAltTextColor = (!empty($menuAltTextColor) && $menuAltTextColor !== '#') ? $menuAltTextColor : '';
	// Sub-Navigation colors
	$subNavText = (!empty($menuSubNavText) && $menuSubNavText !== '#') ? $menuSubNavText : '';
	$subNavBg = (!empty($menuSubNavColor) && $menuSubNavColor !== '#') ? $menuSubNavColor : '';
	// Menu Background Color
	$menuBackground = (!empty($menuBackground) && $menuBackground !== '#') ? $menuBackground : ''; // set to default if no color
	// Default Navbar
	//................................................................
	$style_menuBackground = '';
	$style_menuBackgroundHover = '';
	$style_menuBackgroundOpacity = '';
	$style_menuText = '';
	$style_menuBorder = '';
	$style_borderTop = '';
	if (!empty($menuTextColor) && $menuTextColor !== '#') {
		$menuText = $menuTextColor;
	}
	if (!empty($menuBackground) && $menuBackground !== '#') {
		// color variations...
		$navColor = new Color($menuBackground);
		// Bg
		if (empty($subNavBg)) {
			$subNavBg = ($navColor->isDark()) ? '#'.$navColor->lighten(12) : '#'.$navColor->darken(12);
		}
		// Text
		if (!isset($menuText)) {
			$menuText = ($navColor->isDark()) ? rf_get_as_rgba('#ffffff', 0.9) : rf_get_as_rgba('#000000', 0.9);
		}
		// Opacity
		if (isset($menuOpacity) && is_numeric($menuOpacity) && abs((int)$menuOpacity) < 100) {
			$style_menuBackgroundOpacity = "background-color: ". rf_get_as_rgba($menuBackground, (abs((int)$menuOpacity) * 0.01) ) ."; ";
		}
		$menuBackgroundHover = $subNavBg;
		unset($navColor);

		// styles
		$style_menuBackground = "background-color: ". $menuBackground ."; ";
		$style_menuBackgroundHover = "background-color: ". $menuBackgroundHover ."; ";
		$style_menuBorder = "border-color: ".$menuBackground."; ";
	}
	// Default Navbar Text
	if (!empty($menuText) && $menuText !== '#') {
		$style_menuText = "color: ".$menuText."; ";
		$style_borderTop = "border-top-color: ".$menuText."; ";
		$style_menuToggle = "background-color: ".$menuText."; ";
	}
	if (!empty($style_menuBackground) || !empty($style_menuText)) {
		$menuStyles .= ".top-nav.navbar-default, .navbar-default #navbar-main .navbar-nav > li > a, .navbar-default #navbar-main .navbar-nav > li > a:hover, .navbar-default #navbar-main .navbar-nav > li > a:focus { ". $style_menuBackground . $style_menuText ." }";
		// menu items on responsive menu
		$menuStyles .= "@media (max-width: 959px) { .navbar-default #navbar-main .navbar-nav > li a, .navbar-default #navbar-main .navbar-nav > li a:focus, .navbar-default #navbar-main .navbar-nav .dropdown-menu > li a, .navbar-default #navbar-main .navbar-nav .dropdown-menu > li a:focus {". $style_menuBackground . $style_menuText ."} }";
		if (!empty($style_menuBackgroundOpacity)) {
			$menuStyles .= "body.transparent-nav:not([class*='shrink-nav']) .navbar.top-nav { ". $style_menuBackgroundOpacity ."}";
			$menuStyles .= "body.transparent-nav #navbar-main .navbar-nav > li > a, body.transparent-nav #navbar-main .navbar-nav > li > a:hover, body.transparent-nav #navbar-main .navbar-nav > li > a:focus { background-color: transparent; }";
		}
		$menuStyles .= ".navbar-default .navbar-brand, .navbar-default .navbar-brand:hover, .navbar-default .navbar-brand:focus, .navbar-default .navbar-text, .navbar-default .navbar-toggle .icon-bar { ". $style_menuText ." }";
		if (!empty($style_menuBorder)) {
			$menuStyles .= ".navbar-default .navbar-form { ". $style_menuBorder ." }";
		}
		// sub-menu indicator arrows
		$menuStyles .= ".navbar-default .dropdown-toggle::after, .navbar-default .dropdown.open > .dropdown-toggle:after { ". $style_borderTop ." }";
		// menu toggle
		$menuStyles .= ".navbar-default .navbar-collapse { ". $style_menuText ." }";
		$menuStyles .= ".navbar-default .navbar-toggle .squeeze-inner, .navbar-default .navbar-toggle .squeeze-inner:before, .navbar-default .navbar-toggle .squeeze-inner:after { ". $style_menuToggle ." }";
	}
	// Sub-menu background
	$style_subNavBg = '';
	$style_subNavText = '';
	$style_menuSubNavHover = '';
	$style_subNavBorder = '';
	$style_borderLeft = '';
	$style_borderTop = '';
	if (!empty($subNavBg)) {
		// color variations...
		$SubNavColor = new Color($subNavBg);
		if (empty($subNavText)) {
			$subNavText = ($SubNavColor->isDark()) ? rf_get_as_rgba('#ffffff', 0.9) : rf_get_as_rgba('#000000', 0.9);
		}
		$menuSubNavHover = ($SubNavColor->isDark()) ? 'rgba(255,255,255,.14)' : 'rgba(0,0,0,.07)';
		unset($SubNavColor);
		// styles
		$style_subNavBg = "background-color: ". $subNavBg ."; ";
		$style_menuSubNavHover = "background-color: ". $menuSubNavHover ."; ";
		$style_subNavBorder = "border-color: ". $subNavBg ."; ";
	}
	// Sub-menu text
	if (!empty($subNavText)) {
		$style_subNavText  = "color: ".$subNavText."; ";
		$style_borderLeft  = "border-left-color: ".$subNavText."; border-top-color: transparent !important; ";
		$style_borderRight = "border-right-color: ".$subNavText."; border-top-color: transparent !important; ";
		$style_borderTop   = "border-top-color: ".$subNavText." !important; ";
	}
	if (!empty($style_subNavBg) || !empty($style_subNavText)) {
		$menuStyles .= "@media (min-width: 960px) { ";
			$menuStyles .= ".navbar-default .dropdown-menu, .navbar-default .dropdown-menu > li > a, .navbar-default .dropdown-menu > li > a:focus { ". $style_subNavBg . $style_subNavText ." }";
			$menuStyles .= ".navbar-default .navbar-nav .open .dropdown-menu > li > a, .navbar-default .navbar-nav .open .dropdown-menu > .active > a { ". $style_subNavBg . $style_subNavText ." } ";
			// hover
			$menuStyles .= ".navbar-default #navbar-main .navbar-nav ul.dropdown-menu > li a:hover, .navbar-default #navbar-main .navbar-nav ul.dropdown-menu > li a:focus { ". $style_subNavText ." ". $style_menuSubNavHover ." }";
			// sub-menu indicator arrow
			$menuStyles .= ".navbar-default #navbar-main .dropdown-submenu > a.dropdown-toggle:after, .navbar-default #navbar-main .dropdown-submenu > a.dropdown-toggle:hover:after { ". $style_borderLeft ." }";
			$menuStyles .= "#navbar-main .navbar-right > li:last-child .dropdown-submenu > a.dropdown-toggle:after, #navbar-main .navbar-right > li:nth-last-child(2) .dropdown-submenu > a.dropdown-toggle:after { ". $style_borderRight ." }";
		$menuStyles .= "}";
	}
	// Accent menu item (active item)
	if (!empty($menuAccentColor) && $menuAccentColor !== '#') {
		$menuStyles .= ".top-nav.navbar .navbar-nav > li.active { border-top-color: ".$menuAccentColor."; }";
		$menuStyles .= "@media (max-width: 959px) { .top-nav.navbar .navbar-nav>li.active>a { border-left-color: ".$menuAccentColor."; } }";
	}

	// Add styles to CSS variable
	if (!empty($menuStyles)) {
		$CustomStyles .= $menuStyles;
	}

	// Fonts (body)
	//................................................................
	$font = array();
	if (get_options_data('options-page', 'font-body') == 'google') {
		// get google font data
		$gFont = get_options_data('options-page', 'font-body-google');
		// for properly work in Customize
		if (is_object($gFont)) {
			$gFont = json_decode(json_encode($gFont), true);
		}

		$font['family'] = (isset($gFont['family']) && !empty($gFont['family'])) ? $gFont['family'] : '';
		$font['size'] = (isset($gFont['size']) && !empty($gFont['size'])) ? $gFont['size'] : '';
		$font['color'] = (isset($gFont['color']) && !empty($gFont['color'])) ? $gFont['color'] : '';
	} else {
		// get standard font data
		$font['family'] = get_options_data('options-page', 'font-body-family');
		$font['size']   = get_options_data('options-page', 'font-body-size');
		$font['color']  = get_options_data('options-page', 'font-body-color');
	}

	if (empty($font['family'])) {
		$font['family'] = 'Open Sans';
	}
	if (empty($font['size'])) {
		$font['size'] = '20px';
	}
	if (empty($font['color'])) {
		$font['color'] = '#000000';
	}

	$elementStyles = '';
	$elementStyles_size = '';
	$elementStyles_family = '';
	if ( count($font) ) {
		foreach ($font as $attribute => $style) {
			if (!empty($style)) {
				$property = ($attribute != 'color') ? 'font-'.$attribute : $attribute;
				$elementStyles .= $property.': '. $style .';';
				if ($attribute == 'size') {
					$elementStyles_size .= $property.': '. $style .';';
				}
				if ($attribute == 'family') {
					$elementStyles_family .= $property.': '. $style .';';
				}
			}
		}
	}

	if ( !empty($elementStyles)) {
		// default - all boty font styles
		$CustomStyles .= 'body { '.$elementStyles.' }';
		// only family
		$CustomStyles .= '.heading, .widget-title, .tooltip, .popover { '.$elementStyles_family.' }';
		// size only
		$CustomStyles .= '.widget_recent_author_entries ul li a, .widget_recent_entries ul li a { '.$elementStyles_size.' }';
	}


	// Fonts (heading)
	//................................................................

	$font = array();
	if (get_options_data('options-page', 'font-heading') == 'google') {
		// get google font data
		$gFont = get_options_data('options-page', 'font-heading-google');
		// for properly work in Customize
		if (is_object($gFont)) {
			$gFont = json_decode(json_encode($gFont), true);
		}

		$gFontWeight = explode(',', (isset($gFont['weight']) && !empty($gFont['weight'])) ? $gFont['weight'] : '');
		$font['family'] = $font['family'] = (isset($gFont['family']) && !empty($gFont['family'])) ? $gFont['family'] : '';
		$font['weight'] = (count($gFontWeight)) ? $gFontWeight[0] : '';
		// $font['size']   = $gFont['size'];
		$font['color'] = (isset($gFont['color']) && !empty($gFont['color'])) ? $gFont['color'] : '';
	} else {
		// get standard font data
		$font['family'] = get_options_data('options-page', 'font-heading-family');
		$font['weight'] = get_options_data('options-page', 'font-heading-weight');
		// $font['size']   = get_options_data('options-page', 'font-heading-size');
		$font['color']  = get_options_data('options-page', 'font-heading-color');
	}

	if (empty($font['family'])) {
		$font['family'] = 'Open Sans';
	}
	if (empty($font['weight'])) {
		$font['weight'] = 'normal';
	}
	if (empty($font['color'])) {
		$font['color'] = '#000000';
	}

	$elementStyles = '';
	$elementStyles_color = '';
	$elementStyles_family = '';
	if ( count($font) ) {
		foreach ($font as $attribute => $style) {
			if (!empty($style)) {
				$property = ($attribute != 'color') ? 'font-'.$attribute : $attribute;
				$elementStyles .= $property.': '. $style .';';
				if ($attribute == 'color') {
					$elementStyles_color .= $property.': '. $style .';';
				}
				if ($attribute == 'family') {
					$elementStyles_family .= $property.': '. $style .';';
				}
			}
		}
	}

	if ( !empty($elementStyles)) {
		// All styles for Headings
		$CustomStyles .= 'h1, h2, h3, h4, h5, h6, .h1, .h2, .h3, .h4, .h5, .h6, .search-result h3 { '.$elementStyles.' }';
		// family only
		$CustomStyles .= '.widget_recent_author_entries ul li a, .widget_recent_entries ul li a { '.$elementStyles_family.' }';
		// color only
		$CustomStyles .= '.page-header h1 a, .entry-header h1 a, .page-header h2 a, .entry-header h2 a, .page-header h3 a, .entry-header h3 a, .page-header .entry-title a, .entry-header .entry-title a { '. $elementStyles_color .' }';
	}

	// Create the ".lead" color from heading color
	if ( isset($font['color']) && !empty($font['color']) && $font['color'] !== '#') {
		// color variations...
		$TextColor = new Color($font['color']);
		// lighten / darken
		$textHEX = ($TextColor->isDark()) ? $TextColor->lighten(30) : $TextColor->darken(25);
		$textHSL = $TextColor->hexToHsl($textHEX);
		// desaturate
		if (isset($textHSL['S'])) {
			$textHSL['S'] = $textHSL['S'] * 0.35;
			$textHEX = $TextColor->hslToHex($textHSL);
		}
		unset($TextColor);
		// styles
		$CustomStyles .= '.lead { color: '. rf_get_as_rgba($textHEX, 0.70) .' }';
	}


	// Font (heading sizes)
	//................................................................

	// Array: Tag => Class
	$size_H = array(
		'h1' => '.h1', // for <h1> and <span class=".h1">
		'h2' => '.h2',
		'h3' => '.h3',
		'h4' => '.h4',
		'h5' => '.h5',
		'h6' => '.h6',
	);
	// Headings sizes
	foreach ($size_H as $h => $tags) {
		$size = trim(get_options_data('options-page', 'font-heading-size-'.$h, 'false'));
		if ($size !== 'false' && !empty($size)) {
			if (!strpos($size,'px') && !strpos($size,'em') && !strpos($size,'rem') ) {
				$size .= 'px';
			}
			$h_tags = $h;
			if (!empty($tags)) {
				$h_tags .= ','. $tags;
			}
			$CustomStyles .= $h_tags .' { font-size: '.$size.' }';
		}
	}


	// Other Text Styles
	//................................................................

	$pre_heading = get_options_data('options-page', 'pre-heading-color');
	if (!empty($pre_heading) && $pre_heading != '#') {
		$CustomStyles .= ".pre-heading, p.pre-heading { color: ". $pre_heading ."; }";
	}

	$lead_heading = get_options_data('options-page', 'heading-lead-color');
	if (!empty($lead_heading) && $lead_heading != '#') {
		$CustomStyles .= ".lead, p.lead { color: ". $lead_heading ."; }";
	}


	// Custom CSS (user generated)
	//................................................................

	$userStyles = stripslashes(htmlspecialchars_decode(get_options_data('options-page', 'custom-styles'),ENT_QUOTES));

	// Add styles to CSS variable
	$CustomStyles .= $userStyles;

	// all done!
	return $CustomStyles;

}

endif;

