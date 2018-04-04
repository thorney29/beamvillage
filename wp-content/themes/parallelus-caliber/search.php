<?php
/**
 * The template for displaying Search Results pages.
 */

add_filter('theme_template_has_layout', function(){ return true; }); // we're specifying the content containers

$has_sidebar = is_sidebar_active('sidebar-search');
if ($has_sidebar) {
	$content_class = 'col-md-16';
} else {
	$content_class = 'col-md-20 col-md-offset-2 col-lg-16 col-lg-offset-4';
}

get_header();

do_action( 'theme_before_search' ); ?>

<div class="section-wrapper">
	<div class="container">


		<div class="row">
			<div class="<?php echo esc_attr($content_class) ?>">

				<div class="row">
					<div class="col-sm-24 search-page-field">
						<form role="search" method="get" action="<?php echo esc_url( home_url( '/' ) ) ?>">
							<div class="input-group">
								<input type="text" name="s" class="form-control" placeholder="<?php echo esc_attr_x( 'Type your search', 'placeholder', 'parallelus-caliber') ?>" value="<?php echo esc_attr( get_search_query() ) ?>">
								<span class="input-group-btn">
									<button type="submit" class="btn btn-default"><i class="fa fa-search"></i></button>
								</span>
							</div>
						</form>
					</div>
				</div>
				<p>&nbsp;</p>

			<?php if ( have_posts() ) :

				do_action( 'theme_before_loop' );
				while ( have_posts() ) : the_post();

					// Output the content
					get_template_part( 'content', 'search' );

				endwhile;
				do_action( 'theme_after_loop' );

			else :

				get_template_part( 'no-results', 'search' );

			endif; // end of loop. ?>

			</div>

			<?php if ($has_sidebar) : ?>
			<div class="col-md-8 col-lg-6 col-lg-offset-2">

				<?php get_sidebar('sidebar-search'); ?>

			</div>
			<?php endif; ?>

		</div><!-- /.row -->

	</div>
</div>

<?php if (function_exists( 'rf_get_pagination' ) && rf_get_pagination(false, 4, true) > 1) : ?>
<div class="section-wrapper paging-nav-bottom">
	<div class="container-xl">
		<?php rf_get_pagination(); ?>
	</div>
</div>
<?php

endif;

do_action( 'theme_after_search' );

get_footer(); ?>