<?php
/**
 * Page template for FrontEnd Editor content
 */
?>

<div class="frontend-editor entry-content">
	<article id="page-<?php the_ID(); ?>" <?php post_class(); ?>>

		<?php
		// Show the page title ONLY if the user explicitly specifies it in meta options.
		if ( rf_show_page_title('meta-value') == 'show' ) : ?>
		<?php do_action( 'theme_before_title' ); ?>
		<div class="section-wrapper fee-title-wrapper">
			<div class="container">
				<header class="entry-header">
					<h1 class="entry-title"><?php the_title(); ?></h1>
				</header>
			</div>
		</div>
		<?php do_action( 'theme_after_title' ); ?>
		<?php endif; ?>

		<?php do_action( 'theme_before_content' ); ?>
		<?php the_content(); ?>
		<?php do_action( 'theme_after_content' ); ?>

		<?php
			wp_link_pages( array(
				'before' => '<div class="page-links">' . __( 'Pages:', 'parallelus-caliber' ),
				'after'  => '</div>',
			) );
		?>

	</article><!-- #page-<?php the_ID(); ?> -->
</div><!-- .entry-content -->
