<?php
/**
 * The template part for the default header content
 */
?>

<?php
// Custom Header Content
if (rf_has_custom_header()) {
	?>
	<div id="header" <?php theme_header_class( 'masthead' );?> <?php theme_header_styles() ?>>
		<div class="section-wrapper">
			<div class="<?php echo apply_filters('theme_header_container_class', 'container-xl') ?>">
			<?php
			// Content
			do_action( 'theme_before_header_content' );
			$content_header_template = apply_filters('theme-content-header-template', 'header');
			get_template_part('templates/parts/content', $content_header_template);
			do_action( 'theme_after_header_content' );
			?>
			</div>
		</div><!-- /.section-wrapper -->
	</div><!-- /.masthead -->
	<?php
} // end if rf_has_custom_header()