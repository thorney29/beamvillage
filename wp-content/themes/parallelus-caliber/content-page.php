<?php
/**
 * The template used for displaying page content in page.php and Portfolio CPT (single)
 */

$class_mainSection  = 'col-md-24';
$class_sidebarLeft  = '';
$class_sidebarRight = '';

// Check for custom sidebars from meta options
$meta_options = get_post_custom();

// Sidebar Left
$sidebarLeft = false;
if ( isset($meta_options['theme_custom_sidebar_options_left']) ) {
	$has_sidebarLeft = $meta_options['theme_custom_sidebar_options_left'][0];
	$sidebarLeft = ( !empty($has_sidebarLeft) && $has_sidebarLeft !== 'default' ) ? $has_sidebarLeft : false;
}
// Sidebar Right
$sidebarRight = false;
if ( isset($meta_options['theme_custom_sidebar_options_right']) ) {
	$has_sidebarRight = $meta_options['theme_custom_sidebar_options_right'][0];
	$sidebarRight = ( !empty($has_sidebarRight) && $has_sidebarRight !== 'default' ) ? $has_sidebarRight : false;
}

// Classes for sidebars
if ($sidebarLeft) {
	$class_mainSection  = 'col-md-18 col-md-push-6 col-lg-16 col-lg-push-8';
	$class_sidebarLeft  = 'col-md-6 col-md-pull-18 col-lg-pull-16';
	$class_sidebarRight = '';
}
if ($sidebarRight) {
	$class_mainSection  = 'col-md-18 col-lg-16';
	$class_sidebarLeft  = '';
	$class_sidebarRight = 'col-md-6 col-lg-6 col-lg-push-2';
}
if ($sidebarRight && $sidebarLeft) {
	$class_mainSection  = 'col-md-16 col-lg-12 col-lg-push-6';
	$class_sidebarLeft  = 'col-md-8 col-lg-6 col-lg-pull-12';
	$class_sidebarRight = 'col-md-8 col-lg-6';
}

?>

<div class="<?php echo esc_attr($class_mainSection) ?>">

	<article id="page-<?php the_ID(); ?>" <?php post_class(); ?>>

		<?php if ( rf_show_page_title() ) : ?>
		<?php do_action( 'theme_before_title' ); ?>
		<header class="entry-header">
			<h1 class="entry-title"><?php the_title(); ?></h1>
		</header>
		<?php do_action( 'theme_after_title' ); ?>
		<?php endif; ?>

		<div class="entry-content">
			<?php do_action( 'theme_before_content' ); ?>
			<?php the_content(); ?>
			<?php do_action( 'theme_after_content' ); ?>
			<?php
				wp_link_pages( array(
					'before' => '<div class="page-links">' . __( 'Pages:', 'parallelus-caliber' ),
					'after'  => '</div>',
				) );
			?>
		</div><!-- .entry-content -->

	</article><!-- #page-<?php the_ID(); ?> -->
</div>

<?php

// Sidebar Left
if ( $sidebarLeft ) { ?>
	<div class="sidebar <?php echo esc_attr($class_sidebarLeft) ?>">
		<?php get_sidebar('left'); ?>
	</div><!-- /.sidebar-left -->
	<?php
}

// Sidebar Right
if ( $sidebarRight ) { ?>
	<div class="sidebar <?php echo esc_attr($class_sidebarRight) ?>">
		<?php get_sidebar('right'); ?>
	</div><!-- /.sidebar-left -->
	<?php
}

