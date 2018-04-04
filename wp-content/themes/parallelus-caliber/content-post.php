<?php
/**
 * Default post content used in loops
 */

$post_thumbnailSize = 'theme-blog';
$post_class = '';
$thumb_class = (theme_use_fastload()) ? 'entry-thumbnail-cover fast-load' : 'entry-thumbnail-cover';
$link_url = (get_post_format() == 'link') ? get_post_meta(get_the_ID(), 'postformat_link_url', true) : get_the_permalink();
$title_sup = (get_post_format() == 'link') ? ' <sup><i class="fa fa-external-link-square"></i></sup>' : '';
$link_attr = (get_post_format() == 'link') ? 'target="_blank" rel="nofollow"' : 'rel="bookmark"';
$count = rf_theme_post_loop_count();

// Alternating post style
$post_class .= ($count % 2 == 0) ? ' post-alt' : '';

?>

<article id="post-<?php the_ID(); ?>" <?php post_class( $post_class ); ?>>

	<div class="section-post-thumbnail">

		<div class="entry-thumbnail">
			<figure>
				<img width="1200" height="800" src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAQAAAADCAMAAACDKl70AAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAAZQTFRF////AAAAVcLTfgAAAAF0Uk5TAEDm2GYAAAAOSURBVHjaYmBAAQABBgAADwAB1KgyvAAAAABJRU5ErkJggg==" class="placeholder" alt="placeholder"><!-- 4x3 placeholder-->
			</figure>
		</div><!-- .entry-thumbnail -->
		<?php
		// Media based on Post Format

		switch (get_post_format()) {

			case 'audio': ?>
				<?php if (theme_use_fastload()) : ?>
				<div class="<?php echo esc_attr($thumb_class) ?>" data-image="<?php rf_blog_image_url( get_the_ID(), 'theme-blog' ); ?>">
					<div class="fast-load-img" style="background-image: url(<?php rf_blog_image_url( get_the_ID(), 'theme-blog-micro' ); ?>)"></div>
						<?php theme_audio_player(); ?>
					</div>
				<?php else: ?>
					<div class="<?php echo esc_attr($thumb_class) ?>" style="background-image: url(<?php rf_blog_image_url( get_the_ID(), 'theme-blog' ); ?>)">
						<?php theme_audio_player(); ?>
					</div>
				<?php endif; ?>
				<?php break;

			case 'video': ?>
				<?php if (theme_use_fastload()) : ?>
					<div class="<?php echo esc_attr($thumb_class) ?>" data-image="<?php rf_blog_image_url( get_the_ID(), 'theme-blog' ); ?>" style="background-image: url(<?php rf_blog_image_url( get_the_ID(), 'theme-blog-micro' ); ?>)">
						<?php theme_video_player(); ?>
					</div>
				<?php else: ?>
					<div class="<?php echo esc_attr($thumb_class) ?>" style="background-image: url(<?php rf_blog_image_url( get_the_ID(), 'theme-blog' ); ?>)">
						<?php theme_video_player(); ?>
					</div>
				<?php endif; ?>
				<?php break;

			case 'gallery': ?>
				<div class="entry-thumbnail-cover">
					<div class="post-format-gallery">
						<?php theme_gallery_post_format(); ?>
					</div>
				</div>
				<?php break;

			case 'quote': ?>
				<?php if (theme_use_fastload()) : ?>
					<div class="<?php echo esc_attr($thumb_class) ?>" data-image="<?php rf_blog_image_url( get_the_ID(), 'theme-blog' ); ?>">
						<div class="fast-load-img" style="background-image: url(<?php rf_blog_image_url( get_the_ID(), 'theme-blog-micro' ); ?>)"></div>
					</div>
				<?php else: ?>
					<div class="<?php echo esc_attr($thumb_class) ?>" style="background-image: url(<?php rf_blog_image_url( get_the_ID(), 'theme-blog' ); ?>)"></div>
				<?php endif; ?>
				<?php break;

			default: ?>
				<?php if (theme_use_fastload()) : ?>
					<a href="<?php echo esc_url($link_url) ?>"<?php echo ' '. rf_string($link_attr) .' '; ?>class="<?php echo esc_attr($thumb_class) ?>" data-image="<?php rf_blog_image_url( get_the_ID(), 'theme-blog' ); ?>">
						<div class="fast-load-img" style="background-image: url(<?php rf_blog_image_url( get_the_ID(), 'theme-blog-micro' ); ?>)"></div>
					</a>
				<?php else: ?>
					<a href="<?php echo esc_url($link_url) ?>"<?php echo ' '. rf_string($link_attr) .' '; ?>class="<?php echo esc_attr($thumb_class) ?>"  style="background-image: url(<?php rf_blog_image_url( get_the_ID(), 'theme-blog' ); ?>)"></a>
				<?php endif; ?>
				<?php break;
			} ?>
		<?php

		// Show author avatar or post format icon
		rf_blog_avatar_or_post_format();

		?>
	</div><!-- (no white space)
 --><div class="section-post-content">

		<header class="entry-header">
			<?php
			// Show quote text in header
			if (get_post_format() == 'quote') {
				$quote_text = get_post_meta(get_the_ID(), 'postformat_quote_text', true);
				$source = get_post_meta(get_the_ID(), 'postformat_quote_source', true);
				?>
				<h2 class="entry-title"><?php echo esc_attr($quote_text); ?></h2>
				<?php if (!empty($source)) { ?>
					<cite>&mdash; <?php echo esc_attr($source); ?></cite>
				<?php }
			// Show standard title in header
			} else { ?>
				<h2 class="entry-title"><a href="<?php echo esc_url($link_url); ?>" <?php echo rf_string($link_attr) ?>><?php the_title(); echo rf_string($title_sup) ?></a></h2>
				<?php if (get_post_format() == 'link') { ?>
					<span class="link-url"><a href="<?php echo esc_url($link_url); ?>" <?php echo rf_string($link_attr) ?>><?php echo esc_url($link_url); ?></a></span>
				<?php }
			} ?>
		</header><!-- .entry-header -->

		<?php if (get_post_format() !== 'quote') { ?>
			<div class="entry-content">
				<p>
					<?php echo get_the_excerpt(); ?>
					<?php if (get_post_format() !== 'link') { ?>
						<br>
						<a href="<?php the_permalink(); ?>" rel="bookmark" class="more-link btn btn-default"><?php printf( __( 'More %s&#8594;%s', 'parallelus-caliber' ), '<span class="meta-nav">', '</span>' ); ?></a>
					<?php } ?>
				</p>
			</div>

			<?php if (get_post_format() !== 'link') { ?>
				<footer class="entry-meta">
					<?php rf_blog_post_list_meta(); ?>
				</footer>
			<?php } ?>
		<?php } ?>

	</div>
</article><!-- #post-<?php the_ID(); ?> -->

