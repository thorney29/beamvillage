<?php
/**
 * Theme Header
 *
 * Displays all of the <head> section and everything up to the start of the content output.
 *
 */
?><!DOCTYPE html>
<html <?php language_attributes(); ?> <?php if (function_exists( 'rf_html_cover_class' )) : rf_html_cover_class(); endif; ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php
	$mobile_theme = get_options_data('options-page', 'mobile-theme-color');
	if (!empty( $mobile_theme ) && $mobile_theme !== '#') : ?><meta name="theme-color" content="<?php echo esc_attr($mobile_theme); ?>">
	<meta name="msapplication-navbutton-color" content="<?php echo esc_attr($mobile_theme); ?>">
	<meta name="apple-mobile-web-app-capable" content="yes">
	<meta name="apple-mobile-web-app-status-bar-style" content="black-translucent"><?php endif; ?>
	<link rel="profile" href="http://gmpg.org/xfn/11">
	<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>">
	<?php
	if ( !has_site_icon() ) {
		$favorites = get_options_data('options-page', 'favorites-icon');
		if (!empty( $favorites )) : ?><link rel="shortcut icon" href="<?php echo esc_url($favorites); ?>"><?php endif; ?>
		<?php
		$bookmark = get_options_data('options-page', 'mobile-bookmark');
		if (!empty( $bookmark )) : ?><link rel="apple-touch-icon-precomposed" href="<?php echo esc_url($bookmark); ?>">
		<link rel="icon" sizes="192x192" href="<?php echo esc_url($bookmark); ?>"><?php endif; 
	} else {
		wp_site_icon();
	}
	?>
	<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>

	<?php do_action( 'theme_before_wrapper' ); ?>
	<div id="wrapper">

		<?php do_action( 'theme_before' ); ?>

		<?php
		// Main Menu
		// ----------------------------------------------------------------------
		$show_menu = (is_page_template( 'templates/cover.php' )) ? false : true;
		if ( apply_filters('theme_show_menu', $show_menu) ) {
			do_action( 'theme_before_navbar' );
			get_template_part( 'templates/parts/menu', 'main' );
			do_action( 'theme_after_navbar' );
		}

		?>

		<?php

		// Header Content
		// ----------------------------------------------------------------------
		if ( function_exists('rf_has_custom_header') && rf_has_custom_header() ) {
			// Load a header template based on theme options
			do_action( 'theme_before_header' );
			show_theme_header();
			do_action( 'theme_after_header' );
		}

		?>

		<div id="middle">

		<?php

		// Layout Manager Support - start layout here...
		// ----------------------------------------------------------------------
		/**
		 * We're also using the output_layout action to add a theme specific HTML container
		 * for all template files that do not explicitly state they have pre-defined elements
		 * the applying content containers.
		 */
		do_action('output_layout','start');

	?>