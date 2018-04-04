<?php

#-----------------------------------------------------------------
# Default Sidebars
#-----------------------------------------------------------------

function theme_default_sidebars() {
	// Default sidebar
	register_sidebar( array(
		'name' => __( 'Main Sidebar', 'runway' ),
		'id' => 'sidebar-main',
		'description' => __( 'The default sidebar for general use.', 'runway' ),
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget' => '</aside>',
		'before_title' => '<h3 class="widget-title">',
		'after_title' => '</h3>',
	) );
	// Pages
	register_sidebar( array(
		'name' => __( 'Pages', 'runway' ),
		'id' => 'sidebar-page',
		'description' => __( 'Pages sidebar.', 'runway' ),
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget' => '</aside>',
		'before_title' => '<h3 class="widget-title">',
		'after_title' => '</h3>',
	) );
	// Posts
	register_sidebar( array(
		'name' => __( 'Posts (blog)', 'runway' ),
		'id' => 'sidebar-post',
		'description' => __( 'Blog and posts sidebar.', 'runway' ),
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget' => '</aside>',
		'before_title' => '<h3 class="widget-title">',
		'after_title' => '</h3>',
	) );
	// Search
	register_sidebar( array(
		'name' => __( 'Search', 'runway' ),
		'id' => 'sidebar-search',
		'description' => __( 'Search results page sidebar.', 'runway' ),
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget' => '</aside>',
		'before_title' => '<h3 class="widget-title">',
		'after_title' => '</h3>',
	) );
}
add_action( 'widgets_init', 'theme_default_sidebars' );
