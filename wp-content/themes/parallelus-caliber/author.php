<?php
/**
 * The template for displaying Author pages.
 *
 */

add_filter('theme_template_has_layout', function(){ return true; }); // we're specifying the content containers
$post_type = get_post_type();


get_header();

	do_action( 'theme_before_author' );

	if ( function_exists('rf_has_custom_header') && !rf_has_custom_header() ) {
		// No custom header, so we show the Archive page title

		do_action( 'theme_before_author_title' );
		?>
		<div class="section-wrapper">
			<div class="container-xl">
				<header class="entry-header">
					<div class="author-avatar">
						<?php echo get_avatar( get_the_author_meta('ID'), 100, '', get_the_author_meta('display_name') ); ?>
					</div>
					<p class="pre-heading"><?php _e( 'Author', 'parallelus-caliber' ); ?></p>
					<h1 class="entry-title"><?php echo rf_generate_the_title(); ?></h1>
				</header>

				<?php
				// If a user has filled out their description, show a bio on their entries.
				if ( get_the_author_meta( 'description' ) ) :
					?>
					<div class="row author-description">
						<div class="col-md-18">
							<p class="lead"><?php the_author_meta( 'description' ); ?></p>
						</div>
					</div>
					<?php
				endif;
				?>
			</div>
		</div>
		<?php
		do_action( 'theme_after_author_title' );
	} ?>

	<div class="<?php rf_theme_section_class($post_type) ?>">
		<div class="<?php rf_theme_container_class($post_type) ?>">
			<?php

			if ( have_posts() ) :

				do_action( 'theme_before_loop' );

				// Start the Loop
				while ( have_posts() ) : the_post();
					// Increment for alternating post styles
					rf_theme_post_loop_count('++');

					// Find the correct template file
					$template = 'content-'.$post_type;

					if (locate_template($template .'.php') != '') {
						// Load the template 'content-{post type}-{post format}.php'
						get_template_part($template, get_post_format());
					} else {
						// Fallback to the default 'content-{post format}.php'
						get_template_part( 'content', get_post_format() );
					}

				endwhile;

				rf_theme_post_loop_count('reset');

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

	do_action( 'theme_after_author' );

get_footer();