<?php
/**
 * The template for displaying the footer.
 */
?>

	<?php do_action('output_layout','end'); // Layout Manager - End Layout ?>

	</div> <!-- /#middle -->

	<?php if ( (function_exists('rf_is_cover_template') && !rf_is_cover_template()) || is_page_template( 'templates/cover-with-page.php' ) || is_page_template( 'templates/cover-with-page-and-menu.php' ) ) : ?>

		<?php

		// Footer styles
		$styles = '';
		$container_style['background-color'] = get_options_data('options-page', 'footer-bg-color', '');
		$container_style['background-image'] = get_options_data('options-page', 'footer-bg-image', '');
		foreach ($container_style as $attribute => $style) {
			if ( isset($style) && !empty($style) && $style !== '#') {
				if ($attribute == 'background-image') {
					$style = 'url('. $style .')';
				}
				$styles .= $attribute .':'. $style .';';
			}
		}
		$styles = (!empty($styles)) ? 'style="'.esc_attr($styles).'"' : '';

		?>

		<?php do_action( 'theme_before_footer' ); ?>
		<footer id="footer" <?php echo rf_string($styles); ?>>
			<div class="<?php echo apply_filters('theme_footer_container_class', 'container-xl') ?>">
				<?php

				// Main footer content
				do_action( 'theme_before_footer_content' );
				$footer_content = get_options_data('options-page', 'footer-content-block', '');
				if (!empty($footer_content) && $footer_content !== 'none') {
					the_static_block($footer_content);
				}
				do_action( 'theme_after_footer_content' );
				?>
			</div>
		</footer>
		<?php do_action( 'theme_after_footer' ); ?>

	<?php endif; // !is_page_template "cover" ?>

	</div> <!-- / #wrapper -->

	<?php do_action( 'theme_after' ); ?>
	<?php wp_footer(); ?>

</body>
</html>