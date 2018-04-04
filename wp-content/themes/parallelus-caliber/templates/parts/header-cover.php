<?php
/**
 * The template part for the cover (full screen) background hero image in the header
 */

$has_menu = false;

// Container classes (for extended use)
$container_class = 'overlay';
$container_class = apply_filters('theme_cover_container_class', $container_class);

// Check for FrontEnd editor
$FEE = (class_exists('FLBuilderModel') && FLBuilderModel::is_builder_enabled()) ? true : false;

?>

<!-- Cover element -->
<section id="header">
	<?php

	// Background (featured image)
	$cover_wrapper_style = '';
	if ( has_post_thumbnail() ) {
		$bg_image            = wp_get_attachment_url( get_post_thumbnail_id( $post->ID ) );
		$cover_wrapper_style = 'background-image: url(' . esc_url( $bg_image ) . ');';
	} ?>

	<div class="cover-wrapper" style="<?php echo esc_attr( $cover_wrapper_style ); ?>">
		<div class="cover-container <?php echo esc_attr($container_class) ?>">
			<div class="cover-inner">

				<?php if (!$FEE) { ?><div class="container"><?php } ?>
				<?php while ( have_posts() ) : the_post(); ?>
					<?php
					if ( function_exists('rf_show_page_title') && rf_show_page_title('meta-value') !== 'hide' ) :
						do_action( 'theme_before_title' );
						// Default cover Title ?>
						<header class="page-header entry-header">
							<h1 class="page-title"><?php the_title(); ?></h1>
						</header>
						<?php
						do_action( 'theme_after_title' );
					endif; ?>

					<div class="entry-content">
						<?php do_action( 'theme_before_content' ); ?>
						<?php the_content(); ?>
						<?php do_action( 'theme_after_content' ); ?>
					</div>

				<?php endwhile; ?>
				<?php if (!$FEE) { ?></div><!-- /.container --><?php } ?>

			</div><!-- /.cover-inner -->
		</div><!-- /.cover-container -->
	</div><!-- /.cover-wrapper -->

</section><!-- /#header -->