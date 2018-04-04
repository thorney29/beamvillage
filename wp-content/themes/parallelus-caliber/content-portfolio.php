<?php
/**
 * Default content element for Portfolio item lists
 */

// Defaults and Theme settings
$post_thumbnailSize = 'theme-portfolio';
$post_class = 'portfolio-grid-item';
$node_ID = '';
$size = 'large';
$lightbox = false;
$show_title = true;
$show_excerpt = true;

// Check for FrontEnd editor
if (class_exists('FLBuilderModel') && FLBuilderModel::is_builder_enabled()) {

	// Node ID
	if (isset($module->node)) {
		$node_ID = $module->node;
	}

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

	// Lightbox
	if (isset($settings->link_to) && $settings->link_to == 'lightbox') {
		$lightbox = true;
	}

}


$link = ($lightbox) ? rf_get_blog_image_url(get_the_ID(), 'theme-header') : get_the_permalink();
$lightbox_data = ($lightbox) ? ' data-lightbox="gallery_'.esc_attr($node_ID).'"' : '';
$title_attr = ($lightbox && $show_title) ? ' title="'.esc_attr(get_the_title()).'"' : '';
if (!isset($excerpt_length)) {
	$excerpt_length = ($size == 'small') ? 12 : 24;
}

?>

<a id="post-<?php the_ID(); ?>" <?php post_class( $post_class ); ?> href="<?php echo esc_url($link) ?>"<?php echo ' '. $lightbox_data . $title_attr ?>>
	<?php if (theme_use_fastload()) : ?>
		<div class="item-image fast-load" data-image="<?php rf_blog_image_url( get_the_ID(), 'theme-portfolio' ); ?>">
			<div class="fast-load-img" style="background-image: url(<?php rf_blog_image_url( get_the_ID(), 'theme-portfolio-micro' ); ?>)"></div>
		</div>
	<?php else: ?>
		<div class="item-image" style="background-image: url('<?php rf_blog_image_url( get_the_ID(), 'theme-portfolio' ); ?>')"></div>
	<?php endif; ?>
	<div class="item-content-wrap">
		<div class="content-inner">
			<div class="inner-wrap">
				<h2 class="item-title"><?php if ($show_title) { the_title(); } ?></h2>
				<?php if ($show_excerpt) { ?><p class="item-description"><?php echo customExcerpt(get_the_excerpt(), $excerpt_length); ?></p><?php } ?>
			</div>
		</div>
	</div>
</a><!-- #post-<?php the_ID(); ?> -->
