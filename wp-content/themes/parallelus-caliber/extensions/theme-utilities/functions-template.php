<?php
/**
 * Template Functions
 * ................................................................
 *
 * Functions to perform template actions and output, such as
 * comments, post navigation, etc. These will typically have some
 * form of content output to a template file.
 */


/**
 * SECTION class for index/archive templates
 */
if ( ! function_exists( 'rf_theme_section_class' ) ) :
function rf_theme_section_class( $post_type = '' ) {
	$section_class = 'section-wrapper';
	if ( !empty($post_type) ) {
		if ($post_type == 'post') {
			$section_class .= ' no-padding-x no-padding-y';
		}
	}

	echo apply_filters( 'theme_section_class', esc_attr($section_class) );
}
endif;


/**
 * CONTAINER class for index/archive templates
 */
if ( ! function_exists( 'rf_theme_container_class' ) ) :
function rf_theme_container_class( $post_type = '' ) {
	$container_class = 'container';
	if ( !empty($post_type) ) {
		$container_class .= ' container-'.$post_type;
	}

	echo apply_filters( 'theme_container_class', esc_attr($container_class) );
}
endif;


/**
 * Theme Navbar Logo and Title
 */
if ( ! function_exists( 'rf_theme_logo' ) ) :
function rf_theme_logo() {

	// Class
	$class = 'navbar-brand';

	// Logo
	$logo = '';
	$logo_image = get_options_data('options-page', 'logo', '');
	$has_logo = false;
	if (!empty($logo_image)) {
		$logo = '<img src="'.$logo_image.'" alt="'.esc_attr(get_bloginfo('name', 'display')).'">';
		$logo = apply_filters( 'theme_logo_image', $logo );
		$has_logo = true;
	}

	// Title
	$brand_title = '';
	$brand_title_data = get_options_data('options-page', 'brand-title', '');
	if (!empty($brand_title_data)) {
		if ($has_logo) {
			$brand_title .= ' &nbsp;';
			$class .= ' logo-and-text';
		}
		$brand_title .= $brand_title_data;
		$brand_title  = apply_filters( 'theme_logo_brand_title', $brand_title );
	}

	// Full logo with link
	$logo_link  = apply_filters( 'theme_logo_link', home_url( '/' ));
	$logo_title = apply_filters( 'theme_logo_title', get_bloginfo( 'name', 'display' ));
	$logo_class = apply_filters( 'theme_logo_class', $class);
	$theme_logo = '<a href="'. esc_url( $logo_link ) .'" title="'. esc_attr( $logo_title ) .'" rel="home" class="'. esc_attr( $logo_class ) .'">'. $logo . esc_attr($brand_title) .'</a>';
	$theme_logo = apply_filters( 'theme_logo', $theme_logo);

	echo rf_string($theme_logo);
}
endif;


/**
 * Image URLs for post list Featured Images
 */
if ( ! function_exists( 'rf_get_blog_image_url' ) ) :
function rf_get_blog_image_url( $postID = 0, $size = 'theme-blog' ) {
	global $wpdb;

	// Fallback image
	$img_src = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAQAAAADCAMAAACDKl70AAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAAZQTFRF////AAAAVcLTfgAAAAF0Uk5TAEDm2GYAAAAOSURBVHjaYmBAAQABBgAADwAB1KgyvAAAAABJRU5ErkJggg=='; // as a fallback, data URI for a generic gray image

	if (is_single() && get_post_format() == 'audio') {
		// Alternate fallback image for Audio Post Formats
		$img_src = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAMAAAAoyzS7AAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAAZQTFRFzMzMAAAA0zMzZAAAAAxJREFUeNpiYAAIMAAAAgABT21Z4QAAAABJRU5ErkJggg==';
	}

	// Media File ID
	$thumb_id = ($postID) ? get_post_thumbnail_id($postID) : get_post_thumbnail_id();

	// Use placeholder if no image
	if (empty($thumb_id)) {
		$placeholder = get_options_data('options-page', 'blog-placeholder-image');

		if (!empty($placeholder)) {
			// Look up placeholder ID by image URL in WP DB
			$placeholder_data = $wpdb->get_col($wpdb->prepare("SELECT ID FROM $wpdb->posts WHERE guid='%s';", $placeholder )); // get ID from image URL
			if (is_array($placeholder_data) && !empty($placeholder_data[0])) {
				$thumb_id = $placeholder_data[0];
			}
		}
	}

	// Get the media file data
	$image_data = wp_get_attachment_image_src( $thumb_id, $size ); // returns an array

	if ( is_array($image_data) && isset($image_data[0]) ) {
		$img_src = esc_url($image_data[0]); // URL value
	}

	// Return the image URL
	return $img_src;
}
endif;

// Echo image URL
if ( ! function_exists( 'rf_blog_image_url' ) ) :
function rf_blog_image_url( $postID = 0, $size = 'theme-blog' ) {

	// Output the image URL
	echo rf_get_blog_image_url($postID, $size);
}
endif;


/**
 * Use Fast Load Images
 *
 * Fast Load images are a way to show tiny blury thumbnails in the place
 * of a full size image to fill the area before the larger image can
 * download in the background and replace the thumbnail.
 *
 * This function checks if the feature is turned on or off.
 *
 * @return bool true/false
 */
if ( ! function_exists( 'theme_use_fastload' ) ) :
function theme_use_fastload() {
	return (get_options_data('options-page', 'fast-load', 'on') == 'on') ? true : false;
}
endif;


/**
 * Post list icons - show avatar or post format icon
 */
if ( ! function_exists( 'rf_blog_avatar_or_post_format' ) ) :
function rf_blog_avatar_or_post_format() {

	// Setting from theme options
	$icon_style = get_options_data('options-page', 'post-list-icon');

	if ( $icon_style !== 'avatar') :
		$icon_class = 'fa-file-text';
		switch (get_post_format()) {
			case 'gallery':
				$icon_class = 'fa-image';
				break;
			case 'audio':
				$icon_class = 'fa-volume-up';
				break;
			case 'video':
				$icon_class = 'fa-film';
				break;
			case 'quote':
				$icon_class = 'fa-quote-right';
				break;
			case 'link':
				$icon_class = 'fa-link';
				break;
		}
		$icon_class = apply_filters('blog_post_format_icon_class', $icon_class, get_post_format());
		// show the post format icon ?>
		<div class="post-format-icon">
			<div class="post-icon"><i class="fa <?php echo esc_attr($icon_class); ?>"></i></div>
		</div>
		<?php
	else :
		// show the author avatar ?>
		<div class="author-avatar">
			<a href="<?php echo get_author_posts_url( get_the_author_meta('ID') ) ?>" title="<?php _e('Posts by', 'runway'); echo ' '. get_the_author_meta('display_name'); ?>">
				<?php echo get_avatar( get_the_author_meta('ID'), 100, '', get_the_author_meta('display_name') ); ?>
			</a>
		</div>
		<?php
	endif;
}
endif;


/**
 * Post list meta - footer details
 */
if ( ! function_exists( 'rf_blog_post_list_meta' ) ) :
function rf_blog_post_list_meta() {

	// Output the meta details for post list footers ?>
	<span class="author vcard"><?php the_author_posts_link(); ?></span>
	<span class="sep">/</span>
	<span class="posted-on"><?php echo esc_html(get_the_date()); ?></span>
	<?php
	// Comments link
	if ( comments_open() ) : ?>
		<span class="sep">/</span>
		<span class="comments-link"><?php comments_popup_link(
			__( 'Leave a comment', 'runway' ),
			__( '1 Comment', 'runway' ),
			__( '% Comments', 'runway' ),
			'',
			__( '% Comments', 'runway' )
		);?></span>
		<?php
	// Comments off (link if has posts)
	elseif ( get_comments_number() > 0 ) : ?>
		<span class="sep">/</span>
		<span class="comments-link"><?php comments_number(
			__( 'No comments', 'runway' ),
			__( '1 Comment', 'runway' ),
			__( '% Comments', 'runway' )
		);?></span>
		<?php
	endif;
}
endif;


/**
 * Post loop counter
 */
if ( ! function_exists( 'rf_theme_post_loop_count' ) ) :
function rf_theme_post_loop_count( $action = 'return' ) {
	global $caliber_post_counter;

	if ( $action == 'reset' || empty($caliber_post_counter) ) {
		$caliber_post_counter = 0;
	}
	if ( $action == '++' ) {
		$caliber_post_counter++;
	}

	return $caliber_post_counter;
}
endif;


/**
 * Display navigation to next/previous pages when applicable
 */
if ( ! function_exists( 'rf_next_prev_post_nav' ) ) :
function rf_next_prev_post_nav( $nav_id = 'nav-below' ) {
	global $wp_query, $post;

	// Link content
	$prev_link_text = '<span class="meta-nav"><i class="fa fa-angle-left"></i></span>';
	$next_link_text = '<span class="meta-nav"><i class="fa fa-angle-right"></i></span>';

	// Don't print empty markup on single pages if there's nowhere to navigate.
	if ( is_single() ) {

		if ( get_post_type() == 'portfolio' && function_exists('st_portfolio_next_previous_item') ) {
			// Portfolio post ID
			$previous = st_portfolio_next_previous_item(false, 'previous', true);
			$next = st_portfolio_next_previous_item(false, 'next', true);
			// Portfolio post Object
			$previous = ($previous) ? get_post($previous) : false;
			$next = ($next) ? get_post($next) : false;
			// Portfolio post link
			$link_prev = '<a href="'.get_permalink($previous).'" rel="prev">'.$prev_link_text.'</a>';
			$link_next = '<a href="'.get_permalink($next).'" rel="next">'.$next_link_text.'</a>';
		} else {
			// exclude categories and post formats
			$exclude = array();
			$excludes = array_merge(
				(array) get_options_data('options-page', 'next-prev-exclude-categories'),
				(array) get_options_data('options-page', 'next-prev-exclude-post-formats')
			);
			if (!empty($excludes) && is_array($excludes)) {
				foreach ($excludes as $id) {
					if ($id !== 'all' && $id !== 'no') {
						$exclude[] = (int) $id;
					}
				}
			}
			// Get next/prev posts
			$previous = ( is_attachment() ) ? get_post( $post->post_parent ) : get_adjacent_post( false, implode(',', $exclude), true );
			$next = get_adjacent_post( false, implode(',', $exclude), false );
			// Get link for posts
			$link_prev = '<a href="'.get_permalink($previous).'" rel="prev">'.$prev_link_text.'</a>';
			$link_next = '<a href="'.get_permalink($next).'" rel="next">'.$next_link_text.'</a>';
		}

		if ( ! $next && ! $previous )
			return;
	}

	// Don't print empty markup in archives if there's only one page.
	if ( $wp_query->max_num_pages < 2 && ( is_home() || is_archive() || is_search() ) )
		return;

	$nav_class = ( is_single() ) ? 'post-navigation' : 'paging-navigation';

	?>

	<div class="post-nav-bottom">
		<nav id="<?php echo esc_attr( $nav_id ); ?>" class="<?php echo esc_attr($nav_class); ?>">
			<h2 class="screen-reader-text"><?php _e( 'Post navigation', 'runway' ); ?></h2>
			<ul class="pager">
			<?php

			// navigation links for single posts
			if ( is_single() ) :

				$prev_img = '';
				$next_img = '';
				$prev_img_class = '';
				$next_img_class = '';

				// Titles
				$prev_title = (is_object($previous)) ? get_the_title( $previous->ID ) : '';
				$next_title = (is_object($next)) ? get_the_title( $next->ID ) : '';

				// Excerpts
				$prev_excerpt = (is_object($previous)) ? customExcerpt( rf_get_excerpt_by_id($previous->ID), 30, '', '') : ''; // no trailing '...'
				$next_excerpt = (is_object($next)) ? customExcerpt( rf_get_excerpt_by_id($next->ID), 30, '', '') : ''; // no trailing '...'

				// Labels
				$prev_label = __('Previous', 'runway');
				$next_label = __('Next', 'runway');

				// Look up images and set values for next/previous
				if ( is_attachment() && 'attachment' == $previous->post_type ) {
					return;
				}

				if ( $previous &&  has_post_thumbnail( $previous->ID ) ) {
					$prevthumb = wp_get_attachment_image_src( get_post_thumbnail_id( $previous->ID ), 'theme-blog' );
					$prev_img = esc_url( $prevthumb[0] );
					$prev_img_class = 'w-image';
				}

				if ( $next && has_post_thumbnail( $next->ID ) ) {
					$nextthumb = wp_get_attachment_image_src( get_post_thumbnail_id( $next->ID ), 'theme-blog' );
					$next_img = esc_url( $nextthumb[0] );
					$next_img_class = 'w-image';
				}

				// Show the navigation
				if ( $previous ) {
					echo '<li class="nav-previous previous '.$prev_img_class.'">'.$link_prev.'<div class="meta-nav-description"><div class="meta-nav-img" style="background-image: url('.$prev_img.')"></div><div class="meta-nav-wrap"><p class="heading">'.$prev_label.':</p><h4 class="meta-nav-title">'.$prev_title.'</h4><p class="meta-nav-excerpt">'.$prev_excerpt.'</p></div></div></li>';
				} else {
					// placeholder for previous when there isn't a previous post
					echo '<li class="nav-previous previous invisible"><a href="#"><span class="meta-nav"><i class="fa fa-angle-left"></i></span></a><div class="meta-nav-description"><div class="meta-nav-img"></div><div class="meta-nav-wrap"><h4 class="meta-nav-title">&nbsp;</h4><p class="meta-nav-excerpt"></p></div></div></li>';
				}
				if ( $next ) {
					echo '<li class="nav-next next '.$next_img_class.'">'.$link_next.'<div class="meta-nav-description"><div class="meta-nav-img" style="background-image: url('.$next_img.')"></div><div class="meta-nav-wrap"><p class="heading">'.$next_label.':</p><h4 class="meta-nav-title">'.$next_title.'</h4><p class="meta-nav-excerpt">'.$next_excerpt.'</p></div></div></li>';
				} else {
					// placeholder for next when there isn't a next post
					echo '<li class="nav-next next invisible"><a href="#"><span class="meta-nav"><i class="fa fa-angle-right"></i></span></a><div class="meta-nav-description"><div class="meta-nav-img"></div><div class="meta-nav-wrap"><p class="heading"></p><h4 class="meta-nav-title">&nbsp;</h4><p class="meta-nav-excerpt"></p></div></div></li>';
				}

			elseif ( $wp_query->max_num_pages > 1 && ( is_home() || is_archive() || is_search() ) ) :
				// fallback navigation links for home, archive, and search pages (don't think this is used in theme) ?>

				<?php if ( get_next_posts_link() ) : ?>
				<li class="nav-previous previous"><?php next_posts_link( __( '<span class="meta-nav">&larr;</span> Older posts', 'runway' ) ); ?></li>
				<?php endif; ?>

				<?php if ( get_previous_posts_link() ) : ?>
				<li class="nav-next next"><?php previous_posts_link( __( 'Newer posts <span class="meta-nav">&rarr;</span>', 'runway' ) ); ?></li>
				<?php endif; ?>

			<?php endif; ?>

			</ul>
		</nav><!-- #<?php echo esc_html( $nav_id ); ?> -->
	</div>

	<?php
}
endif; // rf_next_prev_post_nav


/**
 * Template for comments and pingbacks.
 *
 * Used as a callback by wp_list_comments() for displaying the comments.
 */
if ( ! function_exists( 'rf_list_comment' ) ) :
function rf_list_comment( $comment, $args, $depth ) {
	$GLOBALS['comment'] = $comment;

	if ( 'pingback' == $comment->comment_type || 'trackback' == $comment->comment_type ) : ?>

	<li id="comment-<?php comment_ID(); ?>" <?php comment_class( 'media' ); ?>>
		<div class="comment-body">
			<?php _e( 'Pingback:', 'runway' ); ?> <?php comment_author_link(); ?> <?php edit_comment_link( __( 'Edit', 'runway' ), '<span class="edit-link">', '</span>' ); ?>
		</div>

	<?php else : ?>

	<li id="comment-<?php comment_ID(); ?>" <?php comment_class( empty( $args['has_children'] ) ? '' : 'parent' ); ?>>
		<article id="div-comment-<?php comment_ID(); ?>" class="comment-body media">

			<div class="media-body">
				<div class="media-body-wrap panel panel-default">

					<div class="panel-heading clearfix">
						<a class="pull-left" href="#">
							<?php if ( 0 != $args['avatar_size'] ) echo get_avatar( $comment, $args['avatar_size'] ); ?>
						</a>
						<h5 class="media-heading"><?php printf( __( '%s <span class="says">says:</span>', 'runway' ), sprintf( '<cite class="fn">%s</cite>', get_comment_author_link() ) ); ?></h5>
						<div class="comment-meta">
							<a href="<?php echo esc_url( get_comment_link( $comment->comment_ID ) ); ?>">
								<time datetime="<?php comment_time( 'c' ); ?>">
									<?php printf( _x( '%1$s', '1: date, 2: time', 'runway' ), get_comment_date(), get_comment_time() ); ?>
								</time>
							</a>
							<?php edit_comment_link( __( '<span class="glyphicon glyphicon-edit"></span> Edit', 'runway' ), '<span class="edit-link">', '</span>' ); ?>
						</div>
					</div>

					<?php if ( '0' == $comment->comment_approved ) : ?>
						<p class="comment-awaiting-moderation"><?php _e( 'Your comment is awaiting moderation.', 'runway' ); ?></p>
					<?php endif; ?>

					<div class="comment-content panel-body">
						<?php comment_text(); ?>
					</div><!-- .comment-content -->

					<?php comment_reply_link(
						array_merge(
							$args, array(
								'add_below' => 'div-comment',
								'depth' 	=> $depth,
								'max_depth' => $args['max_depth'],
								'before' 	=> '<footer class="reply comment-reply panel-footer">',
								'after' 	=> '</footer><!-- .reply -->'
							)
						)
					); ?>

				</div>
			</div><!-- .media-body -->

		</article><!-- .comment-body -->

	<?php
	endif;
}
endif; // ends check for rf_list_comment()


/**
 * Pages/Posts - Header title show/hide
 */
if ( ! function_exists( 'rf_show_page_title' ) ) :
function rf_show_page_title( $return = true ) {

	$show = true;

	if ( is_page() || is_single() ) {

		// Title in meta options
		$meta_options = get_post_custom( get_queried_object_id() );
		if ( isset($meta_options['theme_custom_layout_metabox_options_title']) ) {
			$title_setting = $meta_options['theme_custom_layout_metabox_options_title'][0];

			if ($return === 'meta-value') // return the setting
				return $title_setting;

			if ( $title_setting === 'hide' || $title_setting === 'in-header' ) {
				$show = false;
			}
		}
	}

	return $show;
}
endif;
