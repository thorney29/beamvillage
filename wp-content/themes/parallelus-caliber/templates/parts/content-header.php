<?php
/**
 * The content of the header
 */

$title = '';
$content = '';
$default_content = '';

// Check for default content
$header_content = get_options_data('options-page', 'header-content-block', '');
if (!empty($header_content) && $header_content !== 'none') {
	$default_content = the_static_block($header_content, array(), false); // get, don't echo
}

// Run filters to get title and content values
$title   = apply_filters('theme_header_title', get_the_title());
$content = apply_filters('theme_header_content', $default_content);


if (!empty($title) || !empty($content)) :


	// Output the title
	if (!empty($title)) {
		do_action('before_header_title'); // make accessible to add custom content before title
		?>
		<h1><?php echo wp_kses_post($title); ?></h1>
		<?php
		do_action('after_header_title'); // make accessible to add custom content after title
	}



	// Output the Content
	if (!empty($content)) {
		do_action('before_header_intro_text'); // make accessible to add custom content after intro text
		echo $content;
		do_action('after_header_intro_text'); // make accessible to add custom content after intro text
	}


endif;