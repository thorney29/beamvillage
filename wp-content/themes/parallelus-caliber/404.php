<?php
/**
 * The template for displaying 404 pages (Not Found).
 * Used as a fallback template when no page is set as the 404 template in theme options.
 *
 */

get_header();

	?>
	<section class="error-container error-404 not-found">

			<header class="entry-header">
				<h2 class="entry-title"><?php _e( 'Whaaaaat??!?!!1', 'parallelus-caliber' ); ?></h2>
				<p class="lead"><?php _e( "It seems the page you're looking for isn't here.", 'parallelus-caliber' ); ?></p>
			</header><!-- .page-header -->

			<div class="404-search-box">
				<p><?php _e( 'Try looking somewhere else and you might get lucky!', 'parallelus-caliber' ); ?></p>
				<?php get_search_form(); ?>
				<br>
				<br>
			</div><!-- /.404-search-box -->

	</section>
	<?php

get_footer();
