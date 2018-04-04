<?php
/**
 * output for loop content
 */

// Defaults and Theme settings
$post_thumbnailSize = 'portfolio';
$post_class = 'portfolio-grid-item';
$size = 'large';
$lightbox = false;
$show_title = true;
$show_excerpt = true;

// Check for FrontEnd editor
if (class_exists('FLBuilderModel') && FLBuilderModel::is_builder_enabled()) {

	// Size
	if (isset($settings->size)) {
		$size = $settings->size;
	}

	// Title
	if (isset($settings->title) && $settings->title == 'hide') {
		$show_title = false;
	}

	// Excerpt
	if (isset($settings->excerpt) && $settings->excerpt == 'hide') {
		$show_excerpt = false;
	}
	if (isset($settings->excerpt_length) && !empty($settings->excerpt_length)) {
		$excerpt_length = $settings->excerpt_length;
	}
}


$link = ($lightbox) ? rf_get_blog_image_url(get_the_ID(), 'header') : get_the_permalink();
$lightbox_data = ($lightbox) ? ' data-lightbox="gallery"' : '';
if (!isset($excerpt_length)) {
	$excerpt_length = ($size == 'small') ? 12 : 24;
}
?>

<a id="post-<?php the_ID(); ?>" <?php post_class( $post_class ); ?> href="<?php echo esc_url($link) ?>"<?php echo  $lightbox_data ?>>
	<div class="item-image fast-load" data-image="<?php rf_blog_image_url( get_the_ID(), 'portfolio' ); ?>">
		<div class="fast-load-img" style="background-image: url(<?php rf_blog_image_url( get_the_ID(), 'portfolio-micro' ); ?>)"></div>
	</div>
	<div class="item-content-wrap">
		<div class="content-inner">
			<div class="inner-wrap">
				<h2 class="item-title"><?php if ($show_title) { the_title(); } ?></h2>
				<?php if ($show_excerpt) { ?><p class="item-description"><?php echo customExcerpt(get_the_excerpt(), $excerpt_length); ?></p><?php } ?>
			</div>
		</div>
	</div>
</a><!-- #post-<?php the_ID(); ?> -->
