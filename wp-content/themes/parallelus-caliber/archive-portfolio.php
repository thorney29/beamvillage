<?php
/**
 * Portfolio Archive Page
 * Used for the index page of the portfolio CPT (plugin: Simple Theme Portfolio)
 */

add_filter('theme_template_has_layout', function(){ return true; }); // we're specifying the content containers

// Section class
$section_class = 'section-wrapper no-padding';

// Container class
$container_class = 'container container-portfolio';

// Portfolio size (for archive-portfolio.php)
$size = get_options_data('options-page', 'portfolio-grid-size', 'medium');
$portfolio_size = ( !empty($size) ) ? $size : 'medium';
if ($post_type == 'portfolio') {
	$container_class .= ' '. $portfolio_size .'-portfolio';
}

get_header();

	do_action( 'theme_before_archive' );

	?>
	<div class="<?php echo esc_attr($section_class)?>">
		<div class="<?php echo esc_attr($container_class)?>">

			<?php
			if ( have_posts() ) :

				do_action( 'theme_before_loop' );

				// Start the Loop
				while ( have_posts() ) : the_post();
					if (locate_template('content-portfolio.php') != '') {
						// Load the template 'content-{post type}-{post format}.php'
						get_template_part('content-portfolio', get_post_format());
					} else {
						// Fallback to the default 'content-{post format}.php'
						get_template_part( 'content', get_post_format() );
					}
				endwhile;

				do_action( 'theme_after_loop' );

			else :
				get_template_part( 'no-results', 'index' );
			endif; // end of loop. ?>

		</div>
	</div>

	<?php if (function_exists( 'rf_get_pagination' ) && rf_get_pagination(false, 4, true) > 1) : ?>
	<div class="section-wrapper paging-nav-bottom">
		<div class="container-xl">
			<?php rf_get_pagination(); ?>
		</div>
	</div>
	<?php endif;

	do_action( 'theme_after_archive' );

get_footer();