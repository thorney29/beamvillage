<?php global $caliber_post_counter;
/**
 * Default post content used in loops
 */

$post_thumbnailSize = 'blog';
$post_class = ''; // 'has-post-thumbnail'; // entry?

// Alternating post style
$post_class .= ($caliber_post_counter % 2 == 0) ? ' post-alt' : '';

?>

<article id="post-<?php the_ID(); ?>" <?php post_class( $post_class ); ?>>

	<div class="section-post-thumbnail">

		<div class="entry-thumbnail">
			<figure>
				<img width="1200" height="800" src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAQAAAADCAMAAACDKl70AAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAAZQTFRF////AAAAVcLTfgAAAAF0Uk5TAEDm2GYAAAAOSURBVHjaYmBAAQABBgAADwAB1KgyvAAAAABJRU5ErkJggg==" class="placeholder" alt="placeholder"><!-- 4x3 placeholder-->
			</figure>
		</div><!-- .entry-thumbnail -->
		<a href="<?php the_permalink(); ?>" class="entry-thumbnail-cover fast-load" data-image="<?php rf_blog_image_url( get_the_ID(), 'blog' ); ?>">
			<?php // echo get_the_post_thumbnail( $post->ID, $post_thumbnailSize ); // if not fastload... ?>
			<div class="fast-load-img" style="background-image: url(<?php rf_blog_image_url( get_the_ID(), 'blog-micro' ); ?>)"></div>
		</a>

		<?php

		// Show author avatar or post format icon
		rf_blog_avatar_or_post_format();

		?>
	</div><!-- (no white space)
 --><div class="section-post-content">

		<header class="entry-header">
			<h2 class="entry-title"><a href="<?php the_permalink(); ?>" rel="bookmark"><?php the_title(); ?></a></h2>
		</header><!-- .entry-header -->

		<div class="entry-content">
			<p>
				<?php echo get_the_excerpt(); ?><br>
				<a href="<?php the_permalink(); ?>" rel="bookmark" class="more-link btn btn-default"><?php printf( __( 'More %s&#8594;%s', 'framework' ), '<span class="meta-nav">', '</span>' ); ?></a>
			</p>
		</div>

		<footer class="entry-meta">
			<?php rf_blog_post_list_meta(); ?>
		</footer>

	</div>
</article><!-- #post-<?php the_ID(); ?> -->

