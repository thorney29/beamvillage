<?php
/**
 * The default page template. Called by "page.php"
 */
// No layout containers (we've provided them in this template)
add_filter('theme_template_has_layout', function(){ return true; });

$template_part = 'default'; // the content template suffix
$FEE = false; // use FrontEnd editor

// Check for FrontEnd editor
if (class_exists('FLBuilderModel') && FLBuilderModel::is_builder_enabled()) {
	// Has FrontEnd editor
	$FEE = true;
	// Template part "content-page-editor"
	$template_part = 'editor';
}

get_header();

	do_action( 'theme_before_page' ); ?>

	<?php while ( have_posts() ) : the_post(); ?>

		<?php get_template_part( 'templates/parts/header-single-post-format', get_post_format() ); // include Post Format media headers ?>

		<?php if (!$FEE) : ?>
		<div class="section-wrapper">
			<div class="container">
				<div class="row">
		<?php endif; ?>

					<?php get_template_part( 'content-page', $template_part ); // load standard page or FEE template ?>

		<?php if (!$FEE) : ?>
				</div>
			</div>
		</div>
		<?php endif; ?>

		<?php
		// Page Footer Content

		$has_comments = (comments_open() || '0' != get_comments_number()) ? true : false;
		$portfolio_details = ('show' == get_post_meta(get_the_ID(),'st_portfolio_show_details','hide')) ? get_post_meta( get_the_ID(), 'st_portfolio_details', '') : false;

		// Show the post footer content area?
		if ( $has_comments || $portfolio_details ) : ?>
			<div class="section-wrapper post-footer">
				<div class="container">
					<div class="row">
						<div class="col-md-24">
							<?php
							// Portfolio Details
							if ($portfolio_details) {
								echo '<div class="footer-portfolio-details">'. wp_kses_post(wpautop(stripslashes(htmlspecialchars_decode($portfolio_details[0])))) .'</div>';
							}

							// Post Comments
							if ($has_comments) {
								comments_template();
							}
							?>
						</div>
					</div>
				</div>
			</div>
		<?php endif; ?>

		<?php
		// Next/Previous post navigation
		if (function_exists( 'rf_next_prev_post_nav' ) && get_post_type() == 'portfolio') {
			rf_next_prev_post_nav( get_post_type().'-nav-below' );
		}

	endwhile; // end of the loop.

	do_action( 'theme_after_page' );

// Footer
get_footer();
