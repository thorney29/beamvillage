<?php
/**
 * Post Formats - Custom headers for the Single Post when using post formats.
 * Displayed in addition to the main theme header.
 */

$post_format = get_post_format();
$show_author = (get_options_data('options-page', 'post-single-author-header', 'show') == 'show' && get_post_type() == 'post') ? 'show-author' : '';

if ( in_array($post_format, array('audio','video','gallery',)) ) :

	do_action( 'theme_before_post_format_header' );

	?>
	<div class="section-wrapper post-<?php echo get_post_format() ?> post-header masthead <?php echo esc_attr($show_author) ?>">

		<?php
		switch( $post_format ) {
			case 'audio':
				// Audio Post Header ?>

				<?php if (theme_use_fastload()) : ?>
					<div class="featuredBlur">
						<div class="blurImage fast-load" data-image="<?php rf_blog_image_url( get_the_ID(), 'theme-blog' ); ?>">
							<div class="fast-load-img" style="background-image: url(<?php rf_blog_image_url( get_the_ID(), 'theme-blog-micro' ); ?>)"></div>
						</div>
					</div>
					<div class="post format-audio">
						<div class="audio-wrap fast-load" data-image="<?php rf_blog_image_url( get_the_ID(), 'theme-portfolio' ); ?>"><?php // uses portfolio 1:1 image for album ?>
							<img class="fast-load-img" src="<?php rf_blog_image_url( get_the_ID(), 'theme-portfolio-micro' ); ?>" width="500" height="500" alt="<?php echo esc_attr(get_the_title()) ?>">
							<?php theme_audio_player(); ?>
						</div>
					</div>
				<?php else: ?>
					<div class="featuredBlur">
						<div class="blurImage" style="background-image: url(<?php rf_blog_image_url( get_the_ID(), 'theme-blog' ); ?>)"></div>
					</div>
					<div class="post format-audio">
						<div class="audio-wrap" style="background-image: url(<?php rf_blog_image_url( get_the_ID(), 'theme-portfolio' ); ?>)"><?php // uses portfolio 1:1 image for album ?>
							<img src="<?php rf_blog_image_url( get_the_ID(), 'theme-portfolio' ); ?>" width="500" height="500" alt="<?php echo esc_attr(get_the_title()) ?>">
							<?php theme_audio_player(); ?>
						</div>
					</div>
				<?php endif; ?>
				<?php
				break;

			case 'video':
				// Video Post Header ?>
				<div class="post format-video">
					<div class="video-wrap">
						<?php theme_video_player(); ?>
					</div>
				</div>
				<?php
				break;

			case 'gallery':
				// Gallery Post Header ?>
				<div class="post format-gallery">
					<div class="gallery-wrap">
						<div class="post-format-gallery multi">
							<?php theme_gallery_post_format(); ?>
						</div>
					</div>
				</div>
				<?php
				break;

			default:
				// nada
		} ?>

		<?php
		// Check the theme options to see if author avatar is enabled
		if (!empty($show_author)) {
			echo get_theme_author_avatar();
		} ?>

	</div>
	<?php

	do_action( 'theme_after_post_format_header' );

endif; ?>