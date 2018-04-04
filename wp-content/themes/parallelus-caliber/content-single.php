<?php
/**
 * Generic single post.
 * Used for any post type not specified with a custom file "content-single-{post type}.php"
 * The default for blog single posts, other CPTs use the 'page.php' template.
 */

$post_thumbnailSize = 'theme-blog';

// Custom options
$meta_options = get_post_custom();

// Sidebars
$class_mainSection  = 'col-md-18 col-md-offset-3'; // 'col-md-20 col-md-offset-2 col-lg-16 col-lg-offset-4';
$class_sidebarLeft  = '';
$class_sidebarRight = '';

// Sidebar Left
$sidebarLeft = false;
if ( isset($meta_options['theme_custom_sidebar_options_left']) ) {
	$has_sidebarLeft = $meta_options['theme_custom_sidebar_options_left'][0];
	$sidebarLeft = ( !empty($has_sidebarLeft) && $has_sidebarLeft !== 'default' ) ? $has_sidebarLeft : false;
}
// Sidebar Right
$sidebarRight = false;
if ( isset($meta_options['theme_custom_sidebar_options_right']) ) {
	$has_sidebarRight = $meta_options['theme_custom_sidebar_options_right'][0];
	$sidebarRight = ( !empty($has_sidebarRight) && $has_sidebarRight !== 'default' ) ? $has_sidebarRight : false;
}

// Classes for sidebars
if ($sidebarLeft) {
	$class_mainSection  = 'col-md-18 col-md-push-6 col-lg-16 col-lg-push-8';
	$class_sidebarLeft  = 'col-md-6 col-md-pull-18 col-lg-pull-16';
	$class_sidebarRight = '';
}
if ($sidebarRight) {
	$class_mainSection  = 'col-md-18 col-lg-16';
	$class_sidebarLeft  = '';
	$class_sidebarRight = 'col-md-6 col-lg-6 col-lg-push-2';
}
if ($sidebarRight && $sidebarLeft) {
	$class_mainSection  = 'col-md-16 col-lg-12 col-lg-push-6';
	$class_sidebarLeft  = 'col-md-8 col-lg-6 col-lg-pull-12';
	$class_sidebarRight = 'col-md-8 col-lg-6';
}

get_template_part('templates/parts/header-single-post-format', get_post_format());


do_action( 'theme_before_single' );

?>

<div class="section-wrapper">
	<div class="container">
		<div class="row">
			<div class="main-section <?php echo esc_attr($class_mainSection) ?>">

				<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

					<?php do_action( 'theme_before_single_title' ); ?>
					<header class="entry-header">
						<?php if ( rf_show_page_title() ) : ?>
							<h1 class="entry-title"><?php the_title(); ?></h1>
						<?php endif; ?>
						<div class="header-meta entry-meta">
							<span class="author vcard"><?php the_author_posts_link(); ?></span>
							<span class="sep">/</span>
							<span class="posted-on"><?php echo esc_html(get_the_date()); ?></span>
						</div>
					</header><!-- .entry-header -->
					<?php do_action( 'theme_after_single_title' ); ?>

					<div class="entry-content single">

						<?php do_action( 'theme_before_content' ); ?>
						<?php the_content(); ?>
						<?php do_action( 'theme_after_content' ); ?>

						<?php
							wp_link_pages( array(
								'before' => '<div class="page-links">' . __( 'Pages:', 'parallelus-caliber' ),
								'after'  => '</div>',
							) );
						?>

					</div>

				</article>

			</div>

			<?php

			// Sidebar Left
			if ( $sidebarLeft ) { ?>
				<div class="sidebar <?php echo esc_attr($class_sidebarLeft) ?>">
					<?php get_sidebar('left'); ?>
				</div>
				<?php
			}

			// Sidebar Right
			if ( $sidebarRight ) { ?>
				<div class="sidebar <?php echo esc_attr($class_sidebarRight) ?>">
					<?php get_sidebar('right'); ?>
				</div>
				<?php
			} ?>

		</div><!-- /.row -->
	</div><!-- /.container -->
</div><!-- /.section-wrapper -->

<?php
// Posts Footer (comments, author details, post meta info, etc...)
if (get_post_type() == 'post') : ?>
	<!-- Post Comments, Author and Meta Details -->
	<div class="section-wrapper post-footer">
		<div class="container">
			<div class="row">

				<?php
				// Alternate column styles if comments are open or we have at least one comment
				if ( comments_open() || '0' != get_comments_number() ) { ?>
				<div class="col-md-9 col-md-push-15 col-lg-7 col-lg-push-17">
				<?php } else { ?>
				<div class="col-md-24">
				<?php } ?>
					<!-- Post Meta Sidebar -->
					<div class="post-meta-sidebar">
						<div class="row">

							<?php
							$show_author_details = get_options_data('options-page', 'post-single-meta-author', 'show');

							if ($show_author_details == 'show') :
								// Alternate column styles if comments are open or we have at least one comment
								if ( comments_open() || '0' != get_comments_number() ) { ?>
								<div class="col-sm-12 col-md-24">
								<?php } else { ?>
								<div class="col-sm-12">
								<?php } ?>

									<h3 class="heading"><?php _e('About the Author', 'parallelus-caliber') ?></h3>

									<div class="author-info">
										<div class="author-avatar">
											<a href="<?php echo get_author_posts_url( get_the_author_meta('ID') ) ?>" title="<?php echo get_the_author_meta('display_name'); ?>">
												<?php echo get_avatar( get_the_author_meta('ID'), 50, '', get_the_author_meta('display_name') ); ?>
											</a>
										</div><!-- .author-avatar -->
										<div class="author-description">
											<h5 class="author-name"><?php the_author_meta('display_name'); ?></h5>
											<?php
											// Author Description
											$desc = get_the_author_meta('description');
											if (!empty($desc)) {
												echo '<p>'. wp_kses_post( customExcerpt($desc, 70) ) .'</p>';
											}
											?>
										</div><!-- .author-description -->
									</div>
								</div>
							<?php
							endif; // show author details

							// Recent Posts from Author
							// ................................................
							$show_recent_posts = get_options_data('options-page', 'post-single-meta-author-posts', 'show');

							// Query posts, check for more from this author
							$authors_posts = get_posts( array(
								'author' => get_the_author_meta('ID'),
								'post__not_in' => array( get_the_ID() ),
								'posts_per_page' => 3
							));

							// This author does have more posts...
							if (count($authors_posts) > 0 && $show_recent_posts == 'show') : ?>

								<?php
								// Alternate column styles if comments are open or we have at least one comment
								if ( comments_open() || '0' != get_comments_number() ) { ?>
								<div class="col-sm-12 col-md-24">
								<?php } else { ?>
								<div class="col-sm-12">
								<?php } ?>

									<div class="widget widget_recent_author_entries">
										<h3 class="widget-title"><?php _e('Recent Posts from', 'parallelus-caliber') ?> <?php the_author_meta('display_name'); ?></h3>
										<ul>
										<?php foreach ( $authors_posts as $authors_post ) { ?>
											<li>
												<a href="<?php echo get_permalink( $authors_post->ID ) ; ?>">
													<?php echo apply_filters( 'the_title', $authors_post->post_title, $authors_post->ID ); ?>
												</a>
												<span class="post-date"><?php echo date(get_option('date_format'),strtotime($authors_post->post_date)); ?></span>
											</li>
										<?php } ?>
										</ul>

										<div class="author-link">
											<a href="<?php echo get_author_posts_url( get_the_author_meta('ID') ) ?>" title="<?php _e('More posts by', 'parallelus-caliber'); echo ' '. get_the_author_meta('display_name'); ?>"><?php _e('See more posts', 'parallelus-caliber') ?> <i class="fa fa-arrow-circle-right"></i></a>
										</div>
									</div>

								</div>
								<?php
								// Reset the post
								wp_reset_postdata();

							endif ; ?>

							<?php

							// Tags and Categories
							// ................................................

							// Check for categories and tags
							$show_categories = get_options_data('options-page', 'post-single-meta-categories', 'show');
							$show_tags = get_options_data('options-page', 'post-single-meta-tags', 'show');
							$category_list = ($show_categories == 'show') ? get_the_category_list( ', &nbsp;' ) : '';
							$tag_list = ($show_tags == 'show') ? get_the_tag_list( '', ', &nbsp;' ) : '';

							// show category and tag sections
							if ( $category_list != '' || $tag_list != '' ) :
								?>
								<div class="col-sm-24">
									<div class="post-meta-info widget">
										<?php

										// Categories
										if ( $category_list != '' ) {
											?>
											<p class="category-links">
												<span class="meta-links">
													<span class="heading"><?php _e('Categories', 'parallelus-caliber') ?>: </span>&nbsp;
													<?php echo wp_kses_post($category_list); ?>
												</span>
											</p>
											<?php
										}

										// Tags
										if ( $tag_list != '' ) {
											?>
											<p class="category-links widget">
												<span class="meta-links">
													<span class="heading"><?php _e('Tags', 'parallelus-caliber') ?>: </span>&nbsp;
													<?php echo wp_kses_post($tag_list); ?>
												</span>
											</p>
											<?php
										}

										?>
									</div>
								</div>
								<?php
							endif; // end Categories and Tags ?>

						</div> <!-- /.row -->

					</div> <!-- /.post-meta-sidebar -->
				</div>

				<?php
				// If comments are open or we have at least one comment, load up the comment template
				if ( comments_open() || '0' != get_comments_number() ) : ?>
				<div class="col-md-15 col-md-pull-9 col-lg-16 col-lg-pull-7">
					<?php comments_template(); ?>
				</div>
				<?php endif; ?>

			</div>
		</div>
	</div>

	<?php
	// Next/Previous post navigation
	if (function_exists( 'rf_next_prev_post_nav' )) {
		rf_next_prev_post_nav( 'nav-below' );
	}

	do_action( 'theme_after_single' );

endif; // get_post_type() == 'post' (for post footer)