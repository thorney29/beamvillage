<?php
/**
 * The template for displaying all single posts.
 *
 */

if (get_post_type() == 'post') {

	// Standard Post Template (single)

	// No layout containers for standard post (we're adding them in the template)
	add_filter('theme_template_has_layout', function(){ return true; });

	get_header();

	// Load the Single Post template
	while ( have_posts() ) : the_post();

		get_template_part( 'content-single', get_post_format() );

	endwhile; // end of the loop.

	get_footer();

} else {

	// Custom Post Types (use default page template)
	get_template_part( 'page', get_post_format() );

}