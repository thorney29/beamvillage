<?php
if (empty($settings->post_type) && FLBuilderModel::is_builder_active()) :
// Empty, no text and we're currently editing ?>
<div class="fl-posts" style="color: #ccccce; border: 3px dashed #dddddf; text-align:center; margin: 4px;">
	<div style="padding: 20px;">
		<i class="fa fa-th fa-3x"></i>
	</div>
</div>
<?php else:
// We have items!
$size = (isset($settings->size)) ? ' '.$settings->size.'-portfolio' : 'medium-portfolio';
?>
<div class="fl-portfolio<?php echo esc_attr($size) ?>">
	<?php

	// Settings
	$args = array();

	$args['paged'] = get_query_var( 'paged', 1 );

	$args['orderby'] = 'menu_order title';
	$args['order'] = 'ASC';
	if (isset($settings->order_by) && !empty($settings->order_by) && $settings->order_by !== 'menu-order') {

		if ($settings->order_by == 'newest') {
			$args['orderby'] = 'date';
			$args['order'] = 'DESC';
		}

		if ($settings->order_by == 'oldest') {
			$args['orderby'] = 'date';
			$args['order'] = 'ASC';
		}

		if ($settings->order_by == 'random') {
			$args['orderby'] = 'rand';
		}
	}

	if (isset($settings->post_type) && !empty($settings->post_type)) {
		$args['post_type'] = $settings->post_type;

		// Categories
		if (isset($settings->items_to_include) && $settings->items_to_include == 'category' && isset($settings->categories) && $settings->categories != 'all') {

			// Blog Categories
			if ($settings->post_type == 'post') {
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

			// Portfolio Categories
			if ($settings->post_type == 'portfolio') {
				$portfolio_include = array();
				$portfolio_exclude = array();

				// Include categories
				if ($settings->categories == 'include' || $settings->categories == 'both') {
					$portfolio_include = explode(',', $settings->category_include);
				}
				// Exclude categories
				if ($settings->categories == 'exclude' || $settings->categories == 'both') {
					// $exclude = explode(',', $settings->category_exclude);
					$portfolio_exclude = explode(',', $settings->category_exclude);
				}

				if (!empty($portfolio_include) || !empty($portfolio_exclude)) {
					//$args['cat'] = implode(',', $portfolio_cat);
					$args['tax_query'] = array();
					if (!empty($portfolio_include) && !empty($portfolio_exclude)) {
						$args['tax_query']['relation'] = 'AND';
					}

					// Include Portfolio Categories
					if (!empty($portfolio_include)) {
						$args['tax_query'][] = array(
							'taxonomy' => 'portfolio-category',
							'field'    => 'term_id',
							'terms'    => array_map('trim', $portfolio_include),
						);
					}

					// Exclude Portfolio Categories
					if (!empty($portfolio_exclude)) {
						$args['tax_query'][] = array(
							'taxonomy' => 'portfolio-category',
							'field'    => 'term_id',
							'terms'    => array_map('trim', $portfolio_exclude),
							'operator' => 'NOT IN',
						);
					}
				}
			}
		}
	}
	if (isset($settings->posts_per_page) && !empty($settings->posts_per_page)) {
		$args['posts_per_page'] = (int) $settings->posts_per_page;
	}
	if (isset($settings->items_to_include) && $settings->items_to_include != 'all') {

		// Include posts by ID
		if ($settings->items_to_include == 'by_id') {
			$by_id = explode(',', str_replace(' ', '', $settings->posts_by_id));
			if (!empty($by_id)) {
				$args['post__in'] = $by_id;
			}
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
			$template = apply_filters('fee_posts_module_content_template', $template_base.'-portfolio', $the_query);

			if (locate_template($template .'-'. get_post_format() .'.php') != '') {
				// Get the template 'content-portfolio-{post type}.php'
				include locate_template($template .'-'. get_post_format() .'.php');
			} elseif (locate_template($template .'.php') != '') {
				// Get the template 'content-portfolio.php'
				include locate_template($template .'.php');
			} elseif (locate_template($template_base .'.php') != '') {
				// Get the template 'content-portfolio.php'
				include locate_template($template_base .'.php');
			} else {
				// Fallback to local file
				include FL_BUILDER_DIR . 'modules/post/includes/content-portfolio.php';
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