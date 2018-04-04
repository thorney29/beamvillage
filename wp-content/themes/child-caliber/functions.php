<?php
function theme_enqueue_styles() {
  wp_enqueue_style( 'theme-style', get_template_directory_uri() . '/style.css' );
  wp_enqueue_style( 'child-style',
    get_stylesheet_directory_uri() . '/style.css',
    array( 'theme-style' ),
    wp_get_theme()->get('Version')
  );
}
add_action( 'wp_enqueue_scripts', 'theme_enqueue_styles' );

