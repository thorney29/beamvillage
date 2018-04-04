<?php
if (empty($settings->post_type) && FLBuilderModel::is_builder_active()) :
// Empty, no text and we're currently editing ?>
<div class="fl-posts" style="color: #ccccce; border: 3px dashed #dddddf; text-align:center; margin: 4px;">
	<div style="padding: 20px;">
		<i class="fa fa-thumb-tack fa-3x"></i>
	</div>
</div>
<?php else:
// We have posts! ?>
<div class="fl-posts">
	<?php

	// Settings
	$args = array();

	$args['paged'] = get_query_var( 'paged', 1 );

	if (isset($settings->post_type) && !empty($settings->post_type)) {
		$args['post_type'] = $settings->post_type;
	}
	if (isset($settings->posts_per_page) && !empty($settings->posts_per_page)) {
		$args['posts_per_page'] = (int) $settings->posts_per_page;
	}
	if (isset($settings->categories) && $settings->categories != 'all') {

		$cat = array();

		// Include categories
		if ($settings->categories == 'include' || $settings->categories == 'both') {
			$cat = array_merge($cat, explode(',', $settings->category_include));
		}
		// Exclude categories
		if ($settings->categories == 'exclude' || $settings->categories == 'both') {
			// $exclude = explode(',', $settings->category_exclude);
			$exclude = explode(',', '-'.implode(',-', explode(',', $settings->category_exclude))); // adds the "-" (minus) to ID's
			$cat = array_merge($cat, $exclude);
		}

		if (!empty($cat)) {
			$args['cat'] = implode(',', $cat);
		}
	}

	// The Query
	$the_query = new WP_Query( $args );

	// Output Posts
	if ( $the_query->have_posts() ) :

		global $caliber_post_counter;
		$caliber_post_counter = 0; // global to track post count in loops

		// Start the Loop
		while ( $the_query->have_posts() ) : $the_query->the_post();

			// Increment for alternating post styles
			$caliber_post_counter++;

			// Find the correct template file
			$template_base = apply_filters('fee_posts_module_content_template_base', 'content', $the_query);
			$template = apply_filters('fee_posts_module_content_template', $template_base.'-post', $the_query);

			if (locate_template($template .'.php') != '') {
				// Load the template 'content-{post type}-{post format}.php'
				get_template_part($template, get_post_format());
			} elseif (locate_template($template_base.'.php') != '') {
				// Fallback to the default 'content-{post format}.php'
				get_template_part( $template_base, get_post_format() );
			} else {
				include FL_BUILDER_DIR . 'modules/post/includes/content-post.php';
			}

		endwhile;

	else :

		get_template_part( 'no-results', 'index' );

	endif; // end of loop.


	// Has Paging
	if (isset($settings->pagination) && $settings->pagination == 'show' && function_exists('rf_get_pagination') && rf_get_pagination($the_query, 4, true) > 1) :
		?>
		<div class="paging-nav-bottom"><?php rf_get_pagination($the_query); ?></div>
		<?php
	endif;

	/* Restore original Post Data */
	wp_reset_postdata();

	?>
</div>
<?php
endif;